<?php

namespace App\Controller\Front\Guild;

use App\Entity\Game\Game;
use App\Entity\Guild\Guild;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GuildController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public TranslatorInterface $translator)
    {
    }

    /**
     * @Route("/guilds", name="front.guild.index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->entityManager->getRepository(Guild::class);

        $guilds = $paginator->paginate($repo->findBy([], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 15);

        return $this->render('@front/guild/index.html.twig', [
            'guilds' => $guilds
        ]);
    }

    /**
     * @Route("/guilds/{slug}", name="front.guild.show")
     * @param string $slug
     * @return Response
     * @throws Exception
     */
    public function show(string $slug): Response
    {
        $guildRepo = $this->entityManager->getRepository(Guild::class);
        $guild = $guildRepo->findOneBy(['slug' => $slug]);

        if (!$guild) {
            return $this->redirectToRoute('front.guild.index');
        }

        return $this->render('@front/guild/show.html.twig', [
            'guild'      => $guild
        ]);
    }
}
