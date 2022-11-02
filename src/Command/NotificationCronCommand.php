<?php

namespace App\Command;

use App\Entity\Notification\Notification;
use App\Repository\Notification\NotificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationCronCommand extends Command
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
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
            ->setName('cron:notification')
            ->setDescription('CRON for Notification')
            ->addArgument('cron', InputArgument::REQUIRED, 'The CRON required for the command.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getArgument('cron')) {
            return Command::INVALID;
        }

        $repository = $this->entityManager->getRepository(Notification::class);

        if ('ancient' === $input->getArgument('cron')) {
            $this->removeAncientNotifications($repository);
        }

        return Command::SUCCESS;
    }

    /**
     * @param NotificationRepository $repository
     *
     * @return void
     */
    private function removeAncientNotifications(NotificationRepository $repository): void
    {
        $notifications = $repository->findAll();

        if ($notifications) {
            $date = new DateTimeImmutable('-3 months');
            foreach ($notifications as $notification) {
                if ($notification->getCreatedAt()->format('Y-m-d') < $date->format('Y-m-d')) {
                    $this->entityManager->remove($notification);
                }
            }

            $this->entityManager->flush();
        }
    }
}
