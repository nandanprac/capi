<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150625051926 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE doctor_consult_settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, timezone VARCHAR(16) NOT NULL, num_ques_day INT NOT NULL, preferred_consultation_timings INT NOT NULL, consultation_days INT NOT NULL, speciality VARCHAR(255) NOT NULL, is_deactivated TINYINT(1) NOT NULL, is_whitelisted TINYINT(1) NOT NULL, status VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE doctor_consult_settings');
    }
}
