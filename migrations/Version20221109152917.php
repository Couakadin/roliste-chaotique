<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109152917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Folder';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_folder (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, owner_id INT NOT NULL, title VARCHAR(64) NOT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EEC56995A977936C (tree_root), INDEX IDX_EEC56995727ACA70 (parent_id), INDEX IDX_EEC569957E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_folder ADD CONSTRAINT FK_EEC56995A977936C FOREIGN KEY (tree_root) REFERENCES rc_folder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rc_folder ADD CONSTRAINT FK_EEC56995727ACA70 FOREIGN KEY (parent_id) REFERENCES rc_folder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rc_folder ADD CONSTRAINT FK_EEC569957E3C61F9 FOREIGN KEY (owner_id) REFERENCES rc_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_folder DROP FOREIGN KEY FK_EEC56995A977936C');
        $this->addSql('ALTER TABLE rc_folder DROP FOREIGN KEY FK_EEC56995727ACA70');
        $this->addSql('ALTER TABLE rc_folder DROP FOREIGN KEY FK_EEC569957E3C61F9');
        $this->addSql('DROP TABLE rc_folder');
    }
}
