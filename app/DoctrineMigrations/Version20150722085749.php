<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722085749 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE doctor_reply_ratings');
        $this->addSql('ALTER TABLE user_info ADD is_enabled TINYINT(1) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX account_idx ON doctor_consult_settings (practo_account_id)');
        $this->addSql('CREATE UNIQUE INDEX doctorId_idx ON doctor_consult_settings (fabric_doctor_id)');
        $this->addSql('ALTER TABLE doctor_questions CHANGE question_id question_id INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE doctor_reply_ratings (id INT AUTO_INCREMENT NOT NULL, doctor_reply_id INT DEFAULT NULL, practo_account_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_E84CA6D722FA3C49 (doctor_reply_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctor_reply_ratings ADD CONSTRAINT FK_E84CA6D722FA3C49 FOREIGN KEY (doctor_reply_id) REFERENCES doctor_replies (id)');
        $this->addSql('DROP INDEX account_idx ON doctor_consult_settings');
        $this->addSql('DROP INDEX doctorId_idx ON doctor_consult_settings');
        $this->addSql('ALTER TABLE doctor_questions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_info DROP is_enabled');
    }
}
