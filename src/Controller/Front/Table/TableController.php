<?php

namespace App\Controller\Front\Table;

use App\Entity\Table\Table;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator
    )
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
     * @return Response
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
            'table' => $table,
            'nextEvents' => $tableRepo->nextEvents($table->getId())
        ]);
    }

    #[Route('/tables/{slug}/add-favorite', name: 'table.favorite', methods: 'post')]
    public function addFavorite(string $slug, Request $request): Response
    {
        $tableRepo = $this->entityManager->getRepository(Table::class);
        $table = $tableRepo->findOneBy(['slug' => $slug]);

        if (!$table || !$this->getUser()) {
            return $this->redirectToRoute('table.index');
        }

        $submittedToken = $request->request->get('token');
        $submittedParticipate = $request->request->get('table_favorite');

        if ($this->isCsrfTokenValid('table-favorite', $submittedToken)) {
            if ('true' === $submittedParticipate) {
                $data = $this->getUser();

                $table->addFavorite($data);
                $this->entityManager->flush();

                $this->addFlash('success', ucfirst($this->translator->trans('flash.table.favorite.add', ['%table%' => $table->getName()])));
            }

            if ('false' === $submittedParticipate) {
                $data = $this->getUser();

                $table->removeFavorite($data);
                $this->entityManager->flush();

                $this->addFlash('success', ucfirst($this->translator->trans('flash.table.favorite.remove', ['%table%' => $table->getName()])));
            }

            return $this->redirectToRoute('table.show', ['slug' => $table->getSlug()]);
        }

        return $this->render('@front/table/show.html.twig', [
            'table' => $table
        ]);
    }
}
