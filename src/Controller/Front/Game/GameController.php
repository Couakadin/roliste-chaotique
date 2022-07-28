<?php

namespace App\Controller\Front\Game;

use App\Entity\Game\Game;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public TranslatorInterface $translator)
    {
    }

    /**
     * @Route("/games", name="front.game.index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->entityManager->getRepository(Game::class);

        $games = $paginator->paginate($repo->findBy([], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 15);

        return $this->render('@front/game/index.html.twig', [
            'games' => $games
        ]);
    }

    /**
     * @Route("/games/{slug}", name="front.game.show")
     * @param string $slug
     * @return Response
     * @throws Exception
     */
    public function show(string $slug): Response
    {
        $gameRepo = $this->entityManager->getRepository(Game::class);
        $game = $gameRepo->findOneBy(['slug' => $slug]);

        if (!$game) {
            return $this->redirectToRoute('front.game.index');
        }

        return $this->render('@front/game/show.html.twig', [
            'game'      => $game
        ]);
    }
}
