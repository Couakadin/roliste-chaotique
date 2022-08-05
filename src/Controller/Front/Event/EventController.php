<?php

namespace App\Controller\Front\Event;

use App\Entity\Event\Event;
use App\Form\Event\EventParticipateType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly TranslatorInterface $translator){ }

    #[Route('/events', name: 'event.index')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $eventRepo = $this->entityManager->getRepository(Event::class);
        $events = $paginator->paginate($eventRepo->findBy([], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 15);

        return $this->render('@front/event/index.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/events/{slug}', name: 'event.show')]
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
}
