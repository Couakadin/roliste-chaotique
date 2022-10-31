<?php

namespace App\Command;

use App\Entity\Zone\Zone;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ZoneCommand extends Command
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
            ->setName('zone:import')
            ->setDescription('Imports the Zone CSV data file');
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
        $io->title('Attempting import of Zones...');

        $file = (file_get_contents(dirname(__DIR__) . '/Assets/zoneDB.json'));

        $json = json_decode($file, true, 512, JSON_THROW_ON_ERROR);

        $connection = $this->entityManager->getConnection();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeQuery('TRUNCATE TABLE rc_zone');
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');

        foreach ($json as $item) {
            $field = $item['fields'];

            $postCode = $field['column_1'];
            $locality = $field['column_2'];
            [$long, $lat] = $field['coordonnees'];
            $longitude = $long;
            $latitude = $lat;

            $zone = (new Zone())
                ->setPostalCode($postCode)
                ->setLocality($locality)
                ->setLongitude($longitude)
                ->setLatitude($latitude);

            $this->entityManager->persist($zone);
        }

        $this->entityManager->flush();


        $io->success('Zones imported cleanly!');

        return true;
    }
}
