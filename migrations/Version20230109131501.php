<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109131501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity UserParameter';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_user_parameter (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_email_reminder TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_4833D4E0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_user_parameter ADD CONSTRAINT FK_4833D4E0A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_user_parameter DROP FOREIGN KEY FK_4833D4E0A76ED395');
        $this->addSql('DROP TABLE rc_user_parameter');
    }
}
