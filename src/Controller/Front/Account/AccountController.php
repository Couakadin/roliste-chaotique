<?php

namespace App\Controller\Front\Account;

use App\Entity\User\User;
use App\Form\User\UserAvatarType;
use App\Form\User\UserPasswordType;
use App\Form\User\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountController extends AbstractController
{
    public function __construct
    (
        public UserPasswordHasherInterface $passwordHasher,
        public EntityManagerInterface $entityManager,
        public TranslatorInterface $translator
    )
    {
    }

    /**
     * @Route("/account/{slug}", name="front.account.index")
     */
    public function index(string $slug = null): Response
    {
        if (is_null($slug)) {
            $user = $this->getUser();
        } else {
            $userRepo = $this->entityManager->getRepository(User::class);
            $user = $userRepo->findOneBy(['slug' => $slug]);
        }

        return $this->render('@front/account/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/account/{slug}/edit", name="front.account.edit")
     */
    public function edit(Request $request, string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('front.account.edit', ['slug' => $this->getUser()->getSlug()]);
        }

        $formProfile = $this->getForm(UserProfileType::class, $user, $request);
        $formAvatar = $this->getForm(UserAvatarType::class, $user, $request);
        $formPassword = $this->getForm(UserPasswordType::class, $user, $request);

        if (($formProfile->isSubmitted() && $formProfile->isValid())
            ||
            ($formAvatar->isSubmitted() && $formAvatar->isValid())) {
            return $this->redirectToRoute('front.account.edit', ['slug' => $user->getSlug()]);
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

            return $this->redirectToRoute('front.account.edit', ['slug' => $user->getSlug()]);
        }

        return $this->render('@front/account/edit.html.twig', [
            'formProfile' => $formProfile->createView(),
            'formAvatar' => $formAvatar->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    /**
     * @Route("/account/{slug}/badges", name="front.account.badge")
     */
    public function badge(string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        return $this->render('@front/account/badge.html.twig', [
            'user' => $user
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
