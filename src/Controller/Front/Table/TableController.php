<?php

namespace App\Controller\Front\Table;

use App\Email\EmailAdmin;
use App\Entity\Table\Table;
use App\Entity\Table\TableInscription;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface    $translator,
        private readonly EmailAdmin             $emailAdmin,
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
     * @param Request $request
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    #[Route('/tables/{slug}', name: 'table.show')]
    public function show(string $slug, Request $request): Response
    {
        $tableRepo = $this->entityManager->getRepository(Table::class);
        $table = $tableRepo->findOneBy(['slug' => $slug]);

        if (!$table) {
            return $this->redirectToRoute('table.index');
        }

        $tableInscriptionRepo = $this->entityManager->getRepository(TableInscription::class);
        $tableInscription = $tableInscriptionRepo->findOneBy(['user' => $this->getUser(), 'table' => $table]);

        $submittedToken = $request->request->get('token');
        $submittedParticipate = $request->request->get('join');

        if ($this->isCsrfTokenValid('join-table', $submittedToken)) {
            if ('true' === $submittedParticipate) {
                $data = $this->getUser();

                $inscription = (new TableInscription())
                    ->setTable($table)
                    ->setUser($data)
                    ->setStatus(TableInscription::STATUS['waiting']);

                $this->entityManager->persist($inscription);
                $this->entityManager->flush();

                $this->addFlash('success', ucfirst($this->translator->trans('flash.table.join.in')));

                $this->emailAdmin->newTableInscriptionAdmin($data, $table);
            }

            if ('false' === $submittedParticipate) {
                $data = $this->getUser();

                $table->removeMember($data);
                $this->entityManager->flush();

                $this->addFlash('success', ucfirst($this->translator->trans('flash.table.join.out')));
            }

            return $this->redirectToRoute('table.show', ['slug' => $table->getSlug()]);
        }

        return $this->render('@front/table/show.html.twig', [
            'table'            => $table,
            'tableInscription' => $tableInscription
        ]);
    }
}
