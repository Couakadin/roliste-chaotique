<?php

namespace App\Controller\Front\Feeds;

use App\Entity\Feeds\Feeds;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/feeds')]
class FeedsController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {

    }

    /**
     * @throws JsonException
     */
    #[Route(name: 'feeds.index')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $feedsRepo = $this->entityManager->getRepository(Feeds::class)
            ->getAllFeeds();

        $feeds = $paginator->paginate($feedsRepo, $request->query->getInt('page', 1), 15);

        return $this->render('@front/feeds/index.html.twig', [
            'feeds' => $feeds
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Route('/more', name: 'feeds.more', methods: ['POST'])]
    public function more(PaginatorInterface $paginator, Request $request): Response
    {
        $feedsRepo = $this->entityManager->getRepository(Feeds::class)
            ->getAllFeeds();

            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $items = $paginator->paginate($feedsRepo, $data['page']['page'], 15)->getItems();

            $response = [];
            foreach ($items as $item) {
                $response[] = ['content' => $item->getContent(), 'author-name' => $item->getAuthor()->getUsername()];
            }

            return new JsonResponse($response);
    }
}
