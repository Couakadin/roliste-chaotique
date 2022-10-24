<?php

namespace App\EventSubscriber;

use App\Entity\Event\Event as EventEntity;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RouterInterface $router) { }

    #[ArrayShape([CalendarEvents::SET_DATA => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @throws Exception
     */
    public function onCalendarSetData(CalendarEvent $calendar)
    {
        // You may want to make a custom query from your database to fill the calendar
        $eventRepo = $this->entityManager->getRepository(EventEntity::class);

        foreach ($eventRepo->findAll() as $event) {
            $calendar->addEvent(new Event(
                $event->getName(),
                new DateTime($event->getStart()->format('d-m-Y H:i')),
                new DateTime($event->getEnd()->format('d-m-Y H:i')),
                $event->getId(), [
                    'backgroundColor' => $event->getBgColor(),
                    'borderColor'     => $event->getBorderColor(),
                    'url'             => $this->router->generate('event.show', ['slug' => $event->getSlug()])
                ]
            ));
        }
    }
}