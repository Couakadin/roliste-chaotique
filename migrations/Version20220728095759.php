<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220728095759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_avatar (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rc_game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT DEFAULT NULL, picture VARCHAR(180) DEFAULT NULL, showcase TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D0D7D5025E237E06 (name), UNIQUE INDEX UNIQ_D0D7D502989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rc_guild (id INT AUTO_INCREMENT NOT NULL, master_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT DEFAULT NULL, picture VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_7FB32F685E237E06 (name), UNIQUE INDEX UNIQ_7FB32F68989D9B62 (slug), INDEX IDX_7FB32F6813B3DB11 (master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rc_guild_user (guild_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EE1A00C65F2131EF (guild_id), INDEX IDX_EE1A00C6A76ED395 (user_id), PRIMARY KEY(guild_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rc_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(52) DEFAULT NULL, expired_at DATETIME DEFAULT NULL, type VARCHAR(25) NOT NULL, INDEX IDX_55C4F3F8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rc_user (id INT AUTO_INCREMENT NOT NULL, avatar_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(180) NOT NULL, slug VARCHAR(128) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, login_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_7E6F32C7E7927C74 (email), UNIQUE INDEX UNIQ_7E6F32C7F85E0677 (username), UNIQUE INDEX UNIQ_7E6F32C7989D9B62 (slug), INDEX IDX_7E6F32C786383B10 (avatar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_141012775E237E06 ON rc_avatar (name)');
        $this->addSql('ALTER TABLE rc_guild ADD CONSTRAINT FK_7FB32F6813B3DB11 FOREIGN KEY (master_id) REFERENCES rc_user (id)');
        $this->addSql('ALTER TABLE rc_guild_user ADD CONSTRAINT FK_EE1A00C65F2131EF FOREIGN KEY (guild_id) REFERENCES rc_guild (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rc_guild_user ADD CONSTRAINT FK_EE1A00C6A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rc_token ADD CONSTRAINT FK_55C4F3F8A76ED395 FOREIGN KEY (user_id) REFERENCES rc_user (id)');
        $this->addSql('ALTER TABLE rc_user ADD CONSTRAINT FK_7E6F32C786383B10 FOREIGN KEY (avatar_id) REFERENCES rc_avatar (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_user DROP FOREIGN KEY FK_7E6F32C786383B10');
        $this->addSql('ALTER TABLE rc_guild_user DROP FOREIGN KEY FK_EE1A00C65F2131EF');
        $this->addSql('ALTER TABLE rc_guild DROP FOREIGN KEY FK_7FB32F6813B3DB11');
        $this->addSql('ALTER TABLE rc_guild_user DROP FOREIGN KEY FK_EE1A00C6A76ED395');
        $this->addSql('ALTER TABLE rc_token DROP FOREIGN KEY FK_55C4F3F8A76ED395');
        $this->addSql('DROP TABLE rc_avatar');
        $this->addSql('DROP TABLE rc_game');
        $this->addSql('DROP TABLE rc_guild');
        $this->addSql('DROP TABLE rc_guild_user');
        $this->addSql('DROP TABLE rc_token');
        $this->addSql('DROP TABLE rc_user');
    }
}
