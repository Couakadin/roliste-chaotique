<?php

namespace App\Controller\Front\Account;

use App\Entity\User\User;
use App\Form\User\UserAvatarType;
use App\Form\User\UserPasswordType;
use App\Form\User\UserProfileType;
use App\Service\BadgeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountController extends AbstractController
{
    public function __construct
    (
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager,
        private readonly TranslatorInterface         $translator,
        private readonly BadgeManager                $badgeManager
    )
    {
    }

    #[Route('/account/{slug}', name: 'account.index')]
    public function index(string $slug = null): Response
    {
        if (is_null($slug)) {
            $user = $this->getUser();
        } else {
            $userRepo = $this->entityManager->getRepository(User::class);
            $user = $userRepo->findOneBy(['slug' => $slug]);
        }

        return $this->render('@front/account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/{slug}/edit', name: 'account.edit')]
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
            // encode the plain password
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
            'formProfile' => $formProfile->createView(),
            'formAvatar' => $formAvatar->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    #[Route('/account/{slug}/badges', name: 'account.badge')]
    public function badge(string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        $badges = $this->badgeManager->getBadgeFor($user);

        return $this->render('@front/account/badge.html.twig', [
            'user'   => $user,
            'badges' => $badges
        ]);
    }

    private function getForm(string $typeClass, User $entity, Request $request): FormInterface
    {
        $form = $this->createForm($typeClass, $entity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.account.edit')));
            //return $this->redirectToRoute('front.account.edit', ['slug' => $entity->getSlug()]);
        }

        return $form;
    }
}
