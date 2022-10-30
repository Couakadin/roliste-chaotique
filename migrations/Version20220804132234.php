<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804132234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Token';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(52) DEFAULT NULL, type VARCHAR(25) NOT NULL, expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_55C4F3F85F37A13B (token), INDEX IDX_55C4F3F8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_token ADD CONSTRAINT FK_55C4F3F8A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rc_token');
    }
}
