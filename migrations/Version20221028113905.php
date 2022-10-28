<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028113905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity EventColor';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_event_color (id INT AUTO_INCREMENT NOT NULL, table_id INT DEFAULT NULL, bg_color VARCHAR(7) NOT NULL, border_color VARCHAR(7) NOT NULL, INDEX IDX_6BA635EBECFF285C (table_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_event_color ADD CONSTRAINT FK_6BA635EBECFF285C FOREIGN KEY (table_id) REFERENCES rc_table (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_event_color DROP FOREIGN KEY FK_6BA635EBECFF285C');
        $this->addSql('DROP TABLE rc_event_color');
    }
}
