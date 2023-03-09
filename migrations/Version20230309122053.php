<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309122053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE track_id_seq CASCADE');
        $this->addSql('ALTER TABLE track DROP CONSTRAINT fk_d6e3f8a61137abcf');
        $this->addSql('DROP TABLE track');
        $this->addSql('ALTER TABLE album DROP duration');
        $this->addSql('ALTER TABLE users ALTER notified DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE track_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE track (id INT NOT NULL, album_id INT NOT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_d6e3f8a61137abcf ON track (album_id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT fk_d6e3f8a61137abcf FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE album ADD duration INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX album_name_key ON album (name)');
        $this->addSql('ALTER TABLE "users" ALTER notified SET DEFAULT true');
    }
}
