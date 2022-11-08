<?php

namespace App\Controller\Front\Account;

use App\Entity\Notification\Notification;
use App\Entity\Storage\Storage;
use App\Entity\User\User;
use App\Form\Storage\StorageType;
use App\Form\User\UserAvatarType;
use App\Form\User\UserPasswordType;
use App\Form\User\UserProfileType;
use App\Form\User\UserStorageType;
use App\Service\BadgeManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route('/account')]
class AccountController extends AbstractController
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param BadgeManager $badgeManager
     * @param KernelInterface $kernel
     * @param UploaderHelper $helper
     */
    public function __construct
    (
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager,
        private readonly TranslatorInterface         $translator,
        private readonly BadgeManager                $badgeManager,
        private readonly KernelInterface             $kernel,
        private readonly UploaderHelper              $helper
    )
    {
    }

    /**
     * @param string|null $slug
     *
     * @return Response
     */
    #[Route('/{slug}', name: 'account.index')]
    public function index(string $slug = null): Response
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

        $lastEvent = $userRepo->findLastEventByUser($user) ?: null;

        return $this->render('@front/account/index.html.twig', [
            'user'      => $user,
            'lastEvent' => $lastEvent
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

        $formProfile = $this->getForm(UserProfileType::class, $user, $request, 'flash.account.edit');
        $formAvatar = $this->getForm(UserAvatarType::class, $user, $request, 'flash.account.edit');
        $formPassword = $this->getForm(UserPasswordType::class, $user, $request, 'flash.account.edit');

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

        return $this->render('@front/account/edit.html.twig', [
            'formProfile'  => $formProfile->createView(),
            'formAvatar'   => $formAvatar->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string|null $slug
     *
     * @return Response
     */
    #[Route('/{slug}/storage', name: 'account.storage')]
    public function storage(Request $request, string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $storageRepo = $this->entityManager->getRepository(Storage::class);
        $storages = $storageRepo->findBy(['user' => $user]);

        $formStorage = $this->getForm(UserStorageType::class, $user, $request, 'flash.account.upload');

        if ($formStorage->isSubmitted() && $formStorage->isValid()) {
            return $this->redirectToRoute('account.storage', ['slug' => $user->getSlug()]);
        }

        return $this->render('@front/account/storage.html.twig', [
            'form' => $formStorage->createView(),
            'storages'    => $storages
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
     * Returns a storage file for display.
     *
     * @param int $id
     *
     * @return BinaryFileResponse|RedirectResponse
     */
    #[Route('/private-file/{id}', name: 'private.file')]
    public function privateFile(int $id): BinaryFileResponse|RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $storage = $this->entityManager->getRepository(Storage::class)
            ->findOneBy(['id' => $id, 'user' => $this->getUser()]);

        if (!$storage) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $asset = $this->helper->asset($storage);

        return new BinaryFileResponse($this->kernel->getProjectDir() . '/templates' . $asset);
    }

    /**
     * @param string $typeClass
     * @param User|Storage $entity
     * @param Request $request
     * @param string $trans
     *
     * @return FormInterface
     */
    private function getForm(string $typeClass, User|Storage $entity, Request $request, string $trans): FormInterface
    {
        $form = $this->createForm($typeClass, $entity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans($trans)));
            //return $this->redirectToRoute('front.account.edit', ['slug' => $entity->getSlug()]);
        }

        return $form;
    }
}
