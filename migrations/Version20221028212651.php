<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028212651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Notification';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, entity_id INT NOT NULL, type VARCHAR(255) NOT NULL, is_read TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_63CE90C9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_notification ADD CONSTRAINT FK_63CE90C9A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_notification DROP FOREIGN KEY FK_63CE90C9A76ED395');
        $this->addSql('DROP TABLE rc_notification');
    }
}
