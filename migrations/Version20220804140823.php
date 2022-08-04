<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804140823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Zone';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_zone (id INT AUTO_INCREMENT NOT NULL, postal_code INT NOT NULL, locality VARCHAR(255) NOT NULL, longitude NUMERIC(10, 8) NOT NULL, latitude NUMERIC(11, 8) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_event ADD zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rc_event ADD CONSTRAINT FK_315D58649F2C3FAB FOREIGN KEY (zone_id) REFERENCES rc_zone (id)');
        $this->addSql('CREATE INDEX IDX_315D58649F2C3FAB ON rc_event (zone_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_event DROP FOREIGN KEY FK_315D58649F2C3FAB');
        $this->addSql('DROP TABLE rc_zone');
        $this->addSql('DROP INDEX IDX_315D58649F2C3FAB ON rc_event');
        $this->addSql('ALTER TABLE rc_event DROP zone_id');
    }
}
