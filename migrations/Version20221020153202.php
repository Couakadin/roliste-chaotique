<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020153202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for Entity Genre';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rc_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE rc_table_genre (table_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_C34732AEECFF285C (table_id), INDEX IDX_C34732AE4296D31F (genre_id), PRIMARY KEY(table_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rc_table_genre ADD CONSTRAINT FK_C34732AEECFF285C FOREIGN KEY (table_id) REFERENCES rc_table (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rc_table_genre ADD CONSTRAINT FK_C34732AE4296D31F FOREIGN KEY (genre_id) REFERENCES rc_genre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rc_table_genre DROP FOREIGN KEY FK_C34732AEECFF285C');
        $this->addSql('ALTER TABLE rc_table_genre DROP FOREIGN KEY FK_C34732AE4296D31F');
        $this->addSql('DROP TABLE rc_table_genre');

        $this->addSql('DROP TABLE rc_genre');
    }
}
