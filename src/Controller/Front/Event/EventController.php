<?php

namespace App\Controller\Front\Event;

use App\Entity\Event\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public TranslatorInterface $translator)
    {
    }

    #[Route('/events', name: 'event.index')]
    public function index(): Response
    {
        return $this->render('@front/event/index.html.twig');
    }

    #[Route('/events/{slug}', name: 'event.show')]
    public function show(string $slug): Response
    {
        $eventRepo = $this->entityManager->getRepository(Event::class);

        return $this->render('@front/event/show.html.twig', [
            'event' => $eventRepo->findOneBy(['slug' => $slug])
        ]);
    }
}
