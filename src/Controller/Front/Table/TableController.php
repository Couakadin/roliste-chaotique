<?php

namespace App\Controller\Front\Table;

use App\Entity\Table\Table;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public TranslatorInterface $translator)
    {
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    #[Route('/tables', name: 'table.index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->entityManager->getRepository(Table::class);

        $tables = $paginator->paginate($repo->findBy([], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 15);

        return $this->render('@front/table/index.html.twig', [
            'tables' => $tables
        ]);
    }

    /**
     * @param string $slug
     *
     * @return Response
     *
     * @throws Exception
     */
    #[Route('/tables/{slug}', name: 'table.show')]
    public function show(string $slug): Response
    {
        $tableRepo = $this->entityManager->getRepository(Table::class);
        $table = $tableRepo->findOneBy(['slug' => $slug]);

        if (!$table) {
            return $this->redirectToRoute('table.index');
        }

        return $this->render('@front/table/show.html.twig', [
            'table'      => $table
        ]);
    }
}
