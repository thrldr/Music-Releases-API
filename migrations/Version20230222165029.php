<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222165029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_band (user_id INT NOT NULL, band_id INT NOT NULL, PRIMARY KEY(user_id, band_id))');
        $this->addSql('CREATE INDEX IDX_325EEE22A76ED395 ON user_band (user_id)');
        $this->addSql('CREATE INDEX IDX_325EEE2249ABEB17 ON user_band (band_id)');
        $this->addSql('ALTER TABLE user_band ADD CONSTRAINT FK_325EEE22A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_band ADD CONSTRAINT FK_325EEE2249ABEB17 FOREIGN KEY (band_id) REFERENCES band (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_band DROP CONSTRAINT FK_325EEE22A76ED395');
        $this->addSql('ALTER TABLE user_band DROP CONSTRAINT FK_325EEE2249ABEB17');
        $this->addSql('DROP TABLE user_band');
        $this->addSql('CREATE UNIQUE INDEX album_name_key ON album (name)');
    }
}
