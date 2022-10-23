<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023151609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Badge';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_badge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, action_name VARCHAR(255) NOT NULL, action_count INT NOT NULL, UNIQUE INDEX UNIQ_F4031ADE5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rc_badge_unlock (id INT AUTO_INCREMENT NOT NULL, badge_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_84C2F5A9F7A2C2FC (badge_id), INDEX IDX_84C2F5A9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_badge_unlock ADD CONSTRAINT FK_84C2F5A9F7A2C2FC FOREIGN KEY (badge_id) REFERENCES rc_badge (id)');
        $this->addSql('ALTER TABLE rc_badge_unlock ADD CONSTRAINT FK_84C2F5A9A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_badge_unlock DROP FOREIGN KEY FK_84C2F5A9F7A2C2FC');
        $this->addSql('ALTER TABLE rc_badge_unlock DROP FOREIGN KEY FK_84C2F5A9A76ED395');
        $this->addSql('DROP TABLE rc_badge');
        $this->addSql('DROP TABLE rc_badge_unlock');
    }
}
