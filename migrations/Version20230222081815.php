<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222081815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE album_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE band_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE track_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE album (id INT NOT NULL, name VARCHAR(255) NOT NULL UNIQUE, release_date DATE NOT NULL, duration INT NOT NULL, type VARCHAR(255) NOT NULL, band_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE band (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE track (id INT NOT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, album_id INT NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE album_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE band_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE track_id_seq CASCADE');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE band');
        $this->addSql('DROP TABLE track');
    }
}
