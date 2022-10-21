<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804132938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Avatar';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_avatar (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_141012775E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_user ADD avatar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rc_user ADD CONSTRAINT FK_7E6F32C786383B10 FOREIGN KEY (avatar_id) REFERENCES rc_avatar (id)');
        $this->addSql('CREATE INDEX IDX_7E6F32C786383B10 ON rc_user (avatar_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_user DROP FOREIGN KEY FK_7E6F32C786383B10');
        $this->addSql('DROP TABLE rc_avatar');
        $this->addSql('DROP INDEX IDX_7E6F32C786383B10 ON rc_user');
        $this->addSql('ALTER TABLE rc_user DROP avatar_id');
    }
}
