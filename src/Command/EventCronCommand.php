<?php

namespace App\Command;

use App\Email\Email;
use App\Entity\Event\Event;
use App\Entity\Notification\Notification;
use App\Repository\Event\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EventCronCommand extends Command
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param Email $email
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Email $email
    )
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('cron:event')
            ->setDescription('CRON for Event')
            ->addArgument('cron', InputArgument::REQUIRED, 'The CRON required for the command.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getArgument('cron')) {
            return Command::INVALID;
        }

        $repository = $this->entityManager->getRepository(Event::class);

        if ('week' === $input->getArgument('cron')) {
            $this->oneWeekBefore($repository);
        }

        return Command::SUCCESS;
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function oneWeekBefore(EventRepository $repository): void
    {
        $events = $repository->findEventsWeekBefore();

        if (0 < count($events)) {
            foreach ($events as $event) {
                $master = $event->getMaster();
                $event->addParticipate($master);
                foreach ($event->getParticipate() as $participate) {
                    $this->email->eventWeekBefore($event, $participate);

                    // With the email, send a notification
                    $notification = (new Notification())
                        ->setUser($participate)
                        ->setEvent($event)
                        ->setIsRead(false)
                        ->setType('event-soon');

                    $this->entityManager->persist($notification);
                }
            }
            $this->entityManager->flush();
        }
    }
}
