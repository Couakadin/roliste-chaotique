<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220805194754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity TableInscription';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_table_inscription (id INT AUTO_INCREMENT NOT NULL, table_id INT NOT NULL, user_id INT NOT NULL, status VARCHAR(15) DEFAULT \'waiting\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_473D66A85405FD2 (table_id), INDEX IDX_473D66AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_table_inscription ADD CONSTRAINT FK_473D66A85405FD2 FOREIGN KEY (table_id) REFERENCES rc_table (id)');
        $this->addSql('ALTER TABLE rc_table_inscription ADD CONSTRAINT FK_473D66AA76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rc_table_inscription');
    }
}
