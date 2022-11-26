<?php

namespace App\Controller;

use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HubController extends AbstractController
{
    #[Route('/hub/{user}', name: 'hub.index')]
    public function index(string $user, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['slug' => $user]);

        return $this->render('@front/hub/index.html.twig', [
            'user' => $user
        ]);
    }
}
