<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150622100530 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE questions_comments_votes (id INT AUTO_INCREMENT NOT NULL, question_comment_id INT DEFAULT NULL, vote SMALLINT NOT NULL, practo_account_id INT NOT NULL, count_of_changed_vote INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_B15002BE9FBDE29B (question_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_info (id INT AUTO_INCREMENT NOT NULL, practo_account_id INT NOT NULL, relative_name VARCHAR(255) DEFAULT NULL, is_relative TINYINT(1) NOT NULL, age INT DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, blood_group VARCHAR(5) DEFAULT NULL, occupation VARCHAR(50) DEFAULT NULL, location VARCHAR(50) DEFAULT NULL, height_in_cms DOUBLE PRECISION DEFAULT NULL, weight_in_kgs DOUBLE PRECISION DEFAULT NULL, allergies LONGTEXT DEFAULT NULL, medications LONGTEXT DEFAULT NULL, prev_diagnosed_conditions LONGTEXT DEFAULT NULL, additional_details LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_comments (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, practo_account_id INT NOT NULL, identifier VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_3C9626261E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions_comments_flag (id INT AUTO_INCREMENT NOT NULL, question_comment_id INT DEFAULT NULL, flag_code VARCHAR(255) NOT NULL, flag_text VARCHAR(255) DEFAULT NULL, practo_account_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_DF6584909FBDE29B (question_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_tags (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, tag VARCHAR(127) NOT NULL, user_defined SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_315279C91E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_views (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, practo_account_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_211B8D831E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_questions (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, practo_account_id INT NOT NULL, state VARCHAR(10) NOT NULL, rejection_reason VARCHAR(100) DEFAULT NULL, rejected_at DATETIME DEFAULT NULL, viewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_45CE353E1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_images (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, url LONGTEXT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_E74291621E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, user_info_id INT DEFAULT NULL, subject VARCHAR(32) DEFAULT NULL, text VARCHAR(360) NOT NULL, speciality VARCHAR(255) DEFAULT NULL, state VARCHAR(20) NOT NULL, view_count INT NOT NULL, share_count INT NOT NULL, viewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_8ADC54D5586DFF2 (user_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_replies_votes (id INT AUTO_INCREMENT NOT NULL, reply_id INT DEFAULT NULL, practo_account_id INT NOT NULL, vote SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_B4AA8EE28A0E4E7F (reply_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_reply_ratings (id INT AUTO_INCREMENT NOT NULL, doctor_reply_id INT DEFAULT NULL, practo_account_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_E84CA6D722FA3C49 (doctor_reply_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_notifications (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, practo_account_id INT NOT NULL, text LONGTEXT NOT NULL, viewed SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_A187FE271E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_notifications (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, practo_account_id INT NOT NULL, text LONGTEXT NOT NULL, viewed SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_5B44780F1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_replies (id INT AUTO_INCREMENT NOT NULL, doctor_question_id INT DEFAULT NULL, text LONGTEXT NOT NULL, rating SMALLINT DEFAULT NULL, viewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, UNIQUE INDEX UNIQ_33AD048799184244 (doctor_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_bookmarks (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, practo_account_id INT NOT NULL, viewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, INDEX IDX_710785DB1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE questions_comments_votes ADD CONSTRAINT FK_B15002BE9FBDE29B FOREIGN KEY (question_comment_id) REFERENCES question_comments (id)');
        $this->addSql('ALTER TABLE question_comments ADD CONSTRAINT FK_3C9626261E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE questions_comments_flag ADD CONSTRAINT FK_DF6584909FBDE29B FOREIGN KEY (question_comment_id) REFERENCES question_comments (id)');
        $this->addSql('ALTER TABLE question_tags ADD CONSTRAINT FK_315279C91E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_views ADD CONSTRAINT FK_211B8D831E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE doctor_questions ADD CONSTRAINT FK_45CE353E1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_images ADD CONSTRAINT FK_E74291621E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)');
        $this->addSql('ALTER TABLE doctor_replies_votes ADD CONSTRAINT FK_B4AA8EE28A0E4E7F FOREIGN KEY (reply_id) REFERENCES doctor_replies (id)');
        $this->addSql('ALTER TABLE doctor_reply_ratings ADD CONSTRAINT FK_E84CA6D722FA3C49 FOREIGN KEY (doctor_reply_id) REFERENCES doctor_replies (id)');
        $this->addSql('ALTER TABLE patient_notifications ADD CONSTRAINT FK_A187FE271E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE doctor_notifications ADD CONSTRAINT FK_5B44780F1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE doctor_replies ADD CONSTRAINT FK_33AD048799184244 FOREIGN KEY (doctor_question_id) REFERENCES doctor_questions (id)');
        $this->addSql('ALTER TABLE question_bookmarks ADD CONSTRAINT FK_710785DB1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5586DFF2');
        $this->addSql('ALTER TABLE questions_comments_votes DROP FOREIGN KEY FK_B15002BE9FBDE29B');
        $this->addSql('ALTER TABLE questions_comments_flag DROP FOREIGN KEY FK_DF6584909FBDE29B');
        $this->addSql('ALTER TABLE doctor_replies DROP FOREIGN KEY FK_33AD048799184244');
        $this->addSql('ALTER TABLE question_comments DROP FOREIGN KEY FK_3C9626261E27F6BF');
        $this->addSql('ALTER TABLE question_tags DROP FOREIGN KEY FK_315279C91E27F6BF');
        $this->addSql('ALTER TABLE question_views DROP FOREIGN KEY FK_211B8D831E27F6BF');
        $this->addSql('ALTER TABLE doctor_questions DROP FOREIGN KEY FK_45CE353E1E27F6BF');
        $this->addSql('ALTER TABLE question_images DROP FOREIGN KEY FK_E74291621E27F6BF');
        $this->addSql('ALTER TABLE patient_notifications DROP FOREIGN KEY FK_A187FE271E27F6BF');
        $this->addSql('ALTER TABLE doctor_notifications DROP FOREIGN KEY FK_5B44780F1E27F6BF');
        $this->addSql('ALTER TABLE question_bookmarks DROP FOREIGN KEY FK_710785DB1E27F6BF');
        $this->addSql('ALTER TABLE doctor_replies_votes DROP FOREIGN KEY FK_B4AA8EE28A0E4E7F');
        $this->addSql('ALTER TABLE doctor_reply_ratings DROP FOREIGN KEY FK_E84CA6D722FA3C49');
        $this->addSql('DROP TABLE questions_comments_votes');
        $this->addSql('DROP TABLE user_info');
        $this->addSql('DROP TABLE question_comments');
        $this->addSql('DROP TABLE questions_comments_flag');
        $this->addSql('DROP TABLE question_tags');
        $this->addSql('DROP TABLE question_views');
        $this->addSql('DROP TABLE doctor_questions');
        $this->addSql('DROP TABLE question_images');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE doctor_replies_votes');
        $this->addSql('DROP TABLE doctor_reply_ratings');
        $this->addSql('DROP TABLE patient_notifications');
        $this->addSql('DROP TABLE doctor_notifications');
        $this->addSql('DROP TABLE doctor_replies');
        $this->addSql('DROP TABLE question_bookmarks');
    }
}
