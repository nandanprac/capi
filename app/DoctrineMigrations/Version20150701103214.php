<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150701103214 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, private_thread_id INT DEFAULT NULL, text VARCHAR(32) NOT NULL, is_doctor_reply TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_8A8E26E94A500993 (private_thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_thread (id INT AUTO_INCREMENT NOT NULL, user_info_id INT DEFAULT NULL, question_id INT DEFAULT NULL, subject VARCHAR(32) NOT NULL, doctor_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_1C0EEA52586DFF2 (user_info_id), INDEX IDX_1C0EEA521E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E94A500993 FOREIGN KEY (private_thread_id) REFERENCES private_thread (id)');
        $this->addSql('ALTER TABLE private_thread ADD CONSTRAINT FK_1C0EEA52586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)');
        $this->addSql('ALTER TABLE private_thread ADD CONSTRAINT FK_1C0EEA521E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('DROP TABLE doctor_replies_flag');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E94A500993');
        $this->addSql('CREATE TABLE doctor_replies_flag (id INT AUTO_INCREMENT NOT NULL, reply_id INT DEFAULT NULL, flag_code VARCHAR(255) NOT NULL, flag_text VARCHAR(255) DEFAULT NULL, practo_account_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_25BECB578A0E4E7F (reply_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctor_replies_flag ADD CONSTRAINT FK_25BECB578A0E4E7F FOREIGN KEY (reply_id) REFERENCES doctor_replies (id)');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE private_thread');
    }
}
