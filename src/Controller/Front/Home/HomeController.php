<?php

namespace App\Controller\Front\Home;

use App\Entity\Game\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * This controller returns the homepage.
     *
     * @Route("/", name="front.home.index")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $game = $entityManager->getRepository(Game::class);

        return $this->render('@front/home/index.html.twig', [
            'games' => $game->findShowcase()
        ]);
    }
}
