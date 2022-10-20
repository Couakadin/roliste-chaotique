<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020161503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity System';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_system (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_table ADD system_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rc_table ADD CONSTRAINT FK_FCDADD85D0952FA5 FOREIGN KEY (system_id) REFERENCES rc_system (id)');
        $this->addSql('CREATE INDEX IDX_FCDADD85D0952FA5 ON rc_table (system_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_table DROP FOREIGN KEY FK_FCDADD85D0952FA5');
        $this->addSql('DROP TABLE rc_system');
        $this->addSql('DROP INDEX IDX_FCDADD85D0952FA5 ON rc_table');
        $this->addSql('ALTER TABLE rc_table DROP system_id');
    }
}
