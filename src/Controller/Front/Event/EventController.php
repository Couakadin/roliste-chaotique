<?php

namespace App\Controller\Front\Event;

use App\Email\Email;
use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Form\Event\EventType;
use App\Repository\Event\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/events')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface    $translator,
        private readonly Email                  $email
    )
    {
    }

    #[Route('/', name: 'event.index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        $eventRepo = $this->entityManager->getRepository(Event::class);
        if ($form->isSubmitted() && $form->isValid()) {
            $events = $paginator->paginate($eventRepo->search($form->getData()), $request->query->getInt('page', 1), 5);
        } else {
            $events = $paginator->paginate($eventRepo->findBy([], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 5);
        }

        return $this->render('@front/event/index.html.twig', [
            'events'     => $events,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route('/event/{slug}', name: 'event.show', methods: ['GET', 'POST'])]
    public function show(string $slug, Request $request): Response
    {
        $eventRepo = $this->entityManager->getRepository(Event::class);
        $event = $eventRepo->findOneBy(['slug' => $slug]);

        if (!$event) {
            return $this->redirectToRoute('event.index');
        }

        $submittedToken = $request->request->get('token');
        $submittedParticipate = $request->request->get('participate');

        if ($this->isCsrfTokenValid('participate-event', $submittedToken)) {
            if ('true' === $submittedParticipate) {
                $data = $this->getUser();

                $event->addParticipate($data);
                $this->entityManager->flush();

                $this->addFlash('success', ucfirst($this->translator->trans('flash.event.participate.add')));
            }

            if ('false' === $submittedParticipate) {
                $data = $this->getUser();

                $event->removeParticipate($data);
                $this->entityManager->flush();

                $this->addFlash('success', ucfirst($this->translator->trans('flash.event.participate.remove')));
            }

            return $this->redirectToRoute('event.show', ['slug' => $event->getSlug()]);
        }

        return $this->render('@front/event/show.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/new', name: 'event.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setMaster($this->getUser());
            $eventRepository->add($event, true);

            $users = $this->entityManager->getRepository(User::class)
                ->findAll();

            foreach ($users as $user) {
                if ($user !== $event->getMaster() && $event->getTable()->getFavorite()->contains($user)) {
                    $this->email->newTableFavorite($user, $event->getTable(), $event, $this->translator->trans('email.new_table_favorite.subject'));
                }
            }

            $this->addFlash('success', ucfirst($this->translator->trans('flash.event.create.success', ['%event%' => $event->getName()])));

            return $this->redirectToRoute('event.show', ['slug' => $event->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('@front/event/new.html.twig', [
            'event' => $event,
            'form'  => $form,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/edit/{slug}', name: 'event.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->add($event, true);

            $users = $this->entityManager->getRepository(User::class)
                ->findAll();

            foreach ($users as $user) {
                if ($user !== $event->getMaster() && $event->getParticipate()->contains($user)) {
                    $this->email->editTableParticipate($user, $event->getTable(), $event, $this->translator->trans('email.edit_table_participate.subject'));
                }
            }

            $this->addFlash('success', ucfirst($this->translator->trans('flash.event.edit.success', ['%event%' => $event->getName()])));

            return $this->redirectToRoute('event.show', ['slug' => $event->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('@front/event/edit.html.twig', [
            'event' => $event,
            'form'  => $form
        ]);
    }

    #[Route('/{id}', name: 'event.delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->addFlash('success', ucfirst($this->translator->trans('flash.event.delete.success', ['%event%' => $event->getName()])));
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('event.index', [], Response::HTTP_SEE_OTHER);
    }
}
