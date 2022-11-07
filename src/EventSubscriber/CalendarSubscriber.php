<?php

namespace App\EventSubscriber;

use App\Entity\Event\Event as EventEntity;
use App\Entity\Event\EventColor;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     */
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RouterInterface $router) { }

    /**
     * @return string[]
     */
    #[ArrayShape([CalendarEvents::SET_DATA => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @param CalendarEvent $calendar
     *
     * @return void
     *
     * @throws Exception
     */
    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        // You may want to make a custom query from your database to fill the calendar
        $eventRepo = $this->entityManager->getRepository(EventEntity::class);
        $eventColorRepo = $this->entityManager->getRepository(EventColor::class);

        foreach ($eventRepo->findAll() as $event) {
            $eventColor = $eventColorRepo->findOneBy(['table' => $event->getTable()]);

            if ($eventColor) {
                $bgColor = $eventColor->getBgColor();
                $borderColor = $eventColor->getBorderColor();
            } else {
                $bgColor = '#000000';
                $borderColor = '#000000';
            }

            $calendar->addEvent(new Event(
                $event->getName(),
                new DateTimeImmutable($event->getStart()->format('d-m-Y H:i')),
                new DateTimeImmutable($event->getEnd()->format('d-m-Y H:i')),
                $event->getId(), [
                    'backgroundColor' => $bgColor,
                    'borderColor'     => $borderColor,
                    'url'             => $this->router->generate('event.show', ['slug' => $event->getSlug()]),
                    'initiation'      => $event->isInitiation()
                ]
            ));
        }
    }
}
