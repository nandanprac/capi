<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150715102531 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_specialities (id INT AUTO_INCREMENT NOT NULL, speciality_id INT DEFAULT NULL, sub_speciality VARCHAR(322) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_E8FDAC273B5A08D7 (speciality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE speciality (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_specialities ADD CONSTRAINT FK_E8FDAC273B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id)');
        $this->addSql("INSERT INTO speciality (id, name) VALUES (1, 'Dermatologist')");
        $this->addSql("INSERT INTO speciality (id, name) VALUES ('2', 'General Physician')");
        $this->addSql("INSERT INTO speciality (id, name) VALUES ('3', 'Orthopedist')");
        $this->addSql(" INSERT INTO speciality (id, name) VALUES ('4', 'Psychiatrist')");
        $this->addSql(" INSERT INTO speciality (id, name) VALUES ('5', 'Dentist')");
        $this->addSql(" INSERT INTO speciality (id, name) VALUES ('6', 'Ayurveda')");
        $this->addSql("INSERT INTO speciality (id, name) VALUES ('7', 'Homeopathy')");


        $this->addSql(" INSERT INTO sub_specialities (id, speciality_id, sub_speciality) VALUES ('1', '3', 'Orthopedist')");
        $this->addSql(" INSERT INTO sub_specialities (id, speciality_id, sub_speciality) VALUES ('2', '3', 'Orthopedic Surgeon')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sub_specialities DROP FOREIGN KEY FK_E8FDAC273B5A08D7');
        $this->addSql('DROP TABLE sub_specialities');
        $this->addSql('DROP TABLE speciality');
    }
}
