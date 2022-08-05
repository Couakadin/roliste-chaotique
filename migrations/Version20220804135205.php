<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804135205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_table (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, showcase TINYINT(1) NOT NULL, picture VARCHAR(180) DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE rc_table_member (table_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F2AC19B8ECFF285C (table_id), INDEX IDX_F2AC19B8A76ED395 (user_id), PRIMARY KEY(table_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_table_member ADD CONSTRAINT FK_F2AC19B8ECFF285C FOREIGN KEY (table_id) REFERENCES rc_table (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rc_table_member ADD CONSTRAINT FK_F2AC19B8A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rc_table');
        $this->addSql('DROP TABLE rc_table_member');
    }
}
