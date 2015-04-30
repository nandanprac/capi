<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150430093138 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE doctor_notifications
            (id INT AUTO_INCREMENT NOT NULL,
                question_id INT DEFAULT NULL,
                doctor_id INT NOT NULL,
                notification_text VARCHAR(255) NOT NULL, is_viewed SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_5B44780F1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE replies (id INT AUTO_INCREMENT NOT NULL, answer_text LONGTEXT NOT NULL, is_selected SMALLINT NOT NULL, viewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, doctorQuestion_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A000672ACCE7A081 (doctorQuestion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, patient_additional_info_id INT DEFAULT NULL, user_id INT NOT NULL, question_text VARCHAR(360) NOT NULL, state VARCHAR(5) NOT NULL, isUserAnonymous SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_8ADC54D5ACE52095 (patient_additional_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_additional_info (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, allergies LONGTEXT NOT NULL, medication LONGTEXT NOT NULL, prev_diagnosed_conditions LONGTEXT NOT NULL, additional_details LONGTEXT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reply_ratings (id INT AUTO_INCREMENT NOT NULL, reply_id INT DEFAULT NULL, user_id INT NOT NULL, rating SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_D3086E0B8A0E4E7F (reply_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_views (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_211B8D831E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_questions (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, doctor_id INT NOT NULL, state VARCHAR(10) NOT NULL, rejection_reason VARCHAR(10) DEFAULT NULL, rejection_at DATETIME DEFAULT NULL, viewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_45CE353E1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_images (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_E74291621E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_notifications (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, patient_ids INT NOT NULL, Notification_txt VARCHAR(255) NOT NULL, isViewed SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_A187FE271E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_bookmarks (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_710785DB1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_tags (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, tag VARCHAR(255) NOT NULL, is_user_defined SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_delete SMALLINT NOT NULL, INDEX IDX_315279C91E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctor_notifications ADD CONSTRAINT FK_5B44780F1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672ACCE7A081 FOREIGN KEY (doctorQuestion_id) REFERENCES doctor_questions (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5ACE52095 FOREIGN KEY (patient_additional_info_id) REFERENCES patient_additional_info (id)');
        $this->addSql('ALTER TABLE reply_ratings ADD CONSTRAINT FK_D3086E0B8A0E4E7F FOREIGN KEY (reply_id) REFERENCES replies (id)');
        $this->addSql('ALTER TABLE question_views ADD CONSTRAINT FK_211B8D831E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE doctor_questions ADD CONSTRAINT FK_45CE353E1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_images ADD CONSTRAINT FK_E74291621E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE patient_notifications ADD CONSTRAINT FK_A187FE271E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_bookmarks ADD CONSTRAINT FK_710785DB1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_tags ADD CONSTRAINT FK_315279C91E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reply_ratings DROP FOREIGN KEY FK_D3086E0B8A0E4E7F');
        $this->addSql('ALTER TABLE doctor_notifications DROP FOREIGN KEY FK_5B44780F1E27F6BF');
        $this->addSql('ALTER TABLE question_views DROP FOREIGN KEY FK_211B8D831E27F6BF');
        $this->addSql('ALTER TABLE doctor_questions DROP FOREIGN KEY FK_45CE353E1E27F6BF');
        $this->addSql('ALTER TABLE question_images DROP FOREIGN KEY FK_E74291621E27F6BF');
        $this->addSql('ALTER TABLE patient_notifications DROP FOREIGN KEY FK_A187FE271E27F6BF');
        $this->addSql('ALTER TABLE question_bookmarks DROP FOREIGN KEY FK_710785DB1E27F6BF');
        $this->addSql('ALTER TABLE question_tags DROP FOREIGN KEY FK_315279C91E27F6BF');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5ACE52095');
        $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672ACCE7A081');
        $this->addSql('DROP TABLE doctor_notifications');
        $this->addSql('DROP TABLE replies');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE patient_additional_info');
        $this->addSql('DROP TABLE reply_ratings');
        $this->addSql('DROP TABLE question_views');
        $this->addSql('DROP TABLE doctor_questions');
        $this->addSql('DROP TABLE question_images');
        $this->addSql('DROP TABLE patient_notifications');
        $this->addSql('DROP TABLE question_bookmarks');
        $this->addSql('DROP TABLE question_tags');
    }
}
