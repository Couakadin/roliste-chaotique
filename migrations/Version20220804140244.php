<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804140244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Event';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_event (id INT AUTO_INCREMENT NOT NULL, table_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, type VARCHAR(15) NOT NULL, start DATETIME NOT NULL, end DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_315D5864ECFF285C (table_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_event ADD bg_color VARCHAR(7) DEFAULT NULL, ADD border_color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE rc_event ADD CONSTRAINT FK_315D5864ECFF285C FOREIGN KEY (table_id) REFERENCES rc_table (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rc_event');
    }
}
