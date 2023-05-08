<?php

namespace App\Command;

use App\Entity\User\User;
use App\Entity\User\UserParameter;
use App\Entity\Zone\Zone;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserParameterCommand extends Command
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('user-parameter:create')
            ->setDescription('Create parameters for all users');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws JsonException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting create parameters...');

        $users = $this->entityManager->getRepository(User::class);

        foreach ($users->findAll() as $user) {
            if (!$user->getUserParameter()) {
                $userParameter = (new UserParameter())
                    ->setUser($user);

                $this->entityManager->persist($userParameter);
            }
        }
        $this->entityManager->flush();

        $io->success('Parameters created!');

        return true;
    }
}
