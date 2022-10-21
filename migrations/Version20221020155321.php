<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020155321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Editor';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_table ADD editor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rc_table ADD CONSTRAINT FK_FCDADD856995AC4C FOREIGN KEY (editor_id) REFERENCES rc_editor (id)');
        $this->addSql('CREATE INDEX IDX_FCDADD856995AC4C ON rc_table (editor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_table DROP FOREIGN KEY FK_FCDADD856995AC4C');
        $this->addSql('DROP TABLE rc_editor');
        $this->addSql('DROP INDEX IDX_FCDADD856995AC4C ON rc_table');
        $this->addSql('ALTER TABLE rc_table DROP editor_id');
    }
}
