<?php

namespace App\Controller\Front\Account;

use App\Entity\Notification\Notification;
use App\Entity\Storage\Storage;
use App\Entity\User\User;
use App\Form\User\UserAvatarType;
use App\Form\User\UserParameterType;
use App\Form\User\UserPasswordType;
use App\Form\User\UserProfileType;
use App\Service\BadgeManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/account')]
class AccountController extends AbstractController
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param BadgeManager $badgeManager
     */
    public function __construct
    (
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager,
        private readonly TranslatorInterface         $translator,
        private readonly BadgeManager                $badgeManager,
    )
    {
    }

    /**
     * @param Request $request
     * @param string|null $slug
     *
     * @return Response
     */
    #[Route('/{slug}', name: 'account.index')]
    public function index(Request $request, string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);

        if (is_null($slug)) {
            $user = $this->getUser();
        } else {
            $user = $userRepo->findOneBy(['slug' => $slug]);
        }

        if (!$user) {
            return $this->redirectToRoute('account.index', [
                'slug' => $this->getUser()->getSlug()
            ], Response::HTTP_PERMANENTLY_REDIRECT);
        }

        $formParameter = $this->createForm(UserParameterType::class, $user->getUserParameter());
        $formParameter->handleRequest($request);

        if ($formParameter->isSubmitted() && $formParameter->isValid()) {
            $formParameter->getData();

            $this->entityManager->flush();
        }

        return $this->renderForm('@front/account/index.html.twig', [
            'user'      => $user,
            'formParameter' => $formParameter
        ]);
    }

    /**
     * @param Request $request
     * @param string|null $slug
     *
     * @return Response
     */
    #[Route('/{slug}/edit', name: 'account.edit')]
    public function edit(Request $request, string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('account.edit', ['slug' => $this->getUser()->getSlug()]);
        }

        $formProfile = $this->getForm(UserProfileType::class, $user, $request);
        $formAvatar = $this->getForm(UserAvatarType::class, $user, $request);
        $formPassword = $this->getForm(UserPasswordType::class, $user, $request);

        if (($formProfile->isSubmitted() && $formProfile->isValid())
            ||
            ($formAvatar->isSubmitted() && $formAvatar->isValid())) {
            return $this->redirectToRoute('account.edit', ['slug' => $user->getSlug()]);
        }

        if (($formPassword->isSubmitted() && $formPassword->isValid())) {
            // Encode the plain password
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $formPassword->get('password')->getData()
                )
            );
            $this->entityManager->flush();

            return $this->redirectToRoute('account.edit', ['slug' => $user->getSlug()]);
        }

        return $this->renderForm('@front/account/edit.html.twig', [
            'formProfile'  => $formProfile,
            'formAvatar'   => $formAvatar,
            'formPassword' => $formPassword,
        ]);
    }

    /**
     * @param string|null $slug
     *
     * @return Response
     */
    #[Route('/{slug}/badges', name: 'account.badge')]
    public function badge(string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if (!$user) {
            return $this->redirectToRoute('account.badge', ['slug' => $this->getUser()->getSlug()]);
        }

        $badges = $this->badgeManager->getAllBadges();

        return $this->render('@front/account/badge.html.twig', [
            'user'   => $user,
            'badges' => $badges
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{id}/delete', name: 'account.delete', methods: 'post')]
    public function deleteAccount(Request $request, int $id): Response
    {
        if (!$this->getUser() || $id !== $this->getUser()->getId()) {
            return $this->redirectToRoute('security.index', [], Response::HTTP_PERMANENTLY_REDIRECT);
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-account', $submittedToken)) {
            $user = $this->entityManager->getRepository(User::class)
                ->find($this->getUser());

            $this->container->get('security.token_storage')->setToken();

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('security.logout');
        }

        return $this->redirectToRoute('home.index');
    }

    /**
     * @param string $slug
     *
     * @return Response
     */
    #[Route('/{slug}/notifications', name: 'account.notifications')]
    public function notifications(string $slug): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('account.edit', [
                'slug' => $this->getUser()->getSlug()
            ], Response::HTTP_PERMANENTLY_REDIRECT);
        }

        $notifications = $this->entityManager->getRepository(Notification::class)
            ->findAllByUser($user);

        return $this->render('@front/account/notification.html.twig', [
            'notifications' => $notifications
        ]);
    }

    /**
     * @param string $typeClass
     * @param User|Storage $entity
     * @param Request $request
     * @return FormInterface
     */
    private function getForm(string $typeClass, User|Storage $entity, Request $request): FormInterface
    {
        $form = $this->createForm($typeClass, $entity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.account.edit')));
        }

        return $form;
    }
}
