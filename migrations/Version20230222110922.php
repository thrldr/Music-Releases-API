<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222110922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE band ADD last_album_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE band ADD previous_album_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE band ADD CONSTRAINT FK_48DFA2EB6956EE51 FOREIGN KEY (last_album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE band ADD CONSTRAINT FK_48DFA2EBEDCB3CB FOREIGN KEY (previous_album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48DFA2EB6956EE51 ON band (last_album_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48DFA2EBEDCB3CB ON band (previous_album_id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D6E3F8A61137ABCF ON track (album_id)');
        $this->addSql('ALTER TABLE album ADD last_album_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE UNIQUE INDEX album_name_key ON album (name)');
        $this->addSql('ALTER TABLE band DROP CONSTRAINT FK_48DFA2EB6956EE51');
        $this->addSql('ALTER TABLE band DROP CONSTRAINT FK_48DFA2EBEDCB3CB');
        $this->addSql('DROP INDEX UNIQ_48DFA2EB6956EE51');
        $this->addSql('DROP INDEX UNIQ_48DFA2EBEDCB3CB');
        $this->addSql('ALTER TABLE band DROP last_album_id');
        $this->addSql('ALTER TABLE band DROP previous_album_id');
        $this->addSql('ALTER TABLE track DROP CONSTRAINT FK_D6E3F8A61137ABCF');
        $this->addSql('DROP INDEX IDX_D6E3F8A61137ABCF');
    }
}
