<?php

namespace App\Command;

use App\Email\Email;
use App\Entity\Event\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
            ->setName('event:cron:email')
            ->setDescription('Send email to users one week before an event');
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
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempt to send emails...');

        $events = $this->entityManager->getRepository(Event::class)
            ->findEventsWeekBefore();

        if (0 < count($events)) {
            foreach ($events as $event) {
                $master = $event->getMaster();
                $event->addParticipate($master);
                foreach ($event->getParticipate() as $participate) {
                    $this->email->eventWeekBefore($event, $participate);
                }
            }

            $io->success('Emails sent perfectly!');
        }

        return true;
    }
}
