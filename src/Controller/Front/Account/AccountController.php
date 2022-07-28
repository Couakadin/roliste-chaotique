<?php

namespace App\Controller\Front\Account;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public TranslatorInterface $translator)
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
    public function edit(string $slug = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('front.account.index');
        }

        return $this->render('@front/account/edit.html.twig');
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
}
