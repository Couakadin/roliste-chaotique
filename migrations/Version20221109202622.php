<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109202622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Storage';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_storage (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, folder_id INT DEFAULT NULL, file_name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, original_name VARCHAR(255) NOT NULL, dimensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_31C8A592989D9B62 (slug), INDEX IDX_31C8A592A76ED395 (user_id), INDEX IDX_31C8A592162CB942 (folder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_storage ADD CONSTRAINT FK_31C8A592A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
        $this->addSql('ALTER TABLE rc_storage ADD CONSTRAINT FK_31C8A592162CB942 FOREIGN KEY (folder_id) REFERENCES rc_folder (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_storage DROP FOREIGN KEY FK_31C8A592A76ED395');
        $this->addSql('ALTER TABLE rc_storage DROP FOREIGN KEY FK_31C8A592162CB942');
        $this->addSql('DROP TABLE rc_storage');
    }
}
