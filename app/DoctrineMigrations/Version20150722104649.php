<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722104649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF14A500993');
        $this->addSql('ALTER TABLE private_thread DROP FOREIGN KEY FK_1C0EEA52586DFF2');
        $this->addSql('ALTER TABLE private_thread DROP FOREIGN KEY FK_1C0EEA521E27F6BF');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5586DFF2');
        $this->addSql('ALTER TABLE questions_comments_votes DROP FOREIGN KEY FK_B15002BE9FBDE29B');
        $this->addSql('ALTER TABLE questions_comments_flag DROP FOREIGN KEY FK_DF6584909FBDE29B');
         $this->addSql('ALTER TABLE question_comments DROP FOREIGN KEY FK_3C9626261E27F6BF');
        $this->addSql('ALTER TABLE question_tags DROP FOREIGN KEY FK_315279C91E27F6BF');
        $this->addSql('ALTER TABLE question_views DROP FOREIGN KEY FK_211B8D831E27F6BF');
        $this->addSql('ALTER TABLE doctor_questions DROP FOREIGN KEY FK_45CE353E1E27F6BF');
        $this->addSql('ALTER TABLE question_images DROP FOREIGN KEY FK_E74291621E27F6BF');
        $this->addSql('ALTER TABLE question_bookmarks DROP FOREIGN KEY FK_710785DB1E27F6BF');
        $this->addSql('ALTER TABLE doctor_replies_votes DROP FOREIGN KEY FK_B4AA8EE28A0E4E7F');
        $this->addSql('ALTER TABLE question_views CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE conversations CHANGE private_thread_id private_thread_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions_comments_votes CHANGE question_comment_id question_comment_id INT NOT NULL');
        $this->addSql('ALTER TABLE question_tags CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions_comments_flag CHANGE question_comment_id question_comment_id INT NOT NULL');
        $this->addSql('ALTER TABLE question_bookmarks CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions CHANGE user_info_id user_info_id INT NOT NULL');
        $this->addSql('ALTER TABLE question_comments CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_thread CHANGE question_id question_id INT NOT NULL, CHANGE user_info_id user_info_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions_comments_votes ADD CONSTRAINT FK_B15002BE9FBDE29B FOREIGN KEY (question_comment_id) REFERENCES question_comments (id)');
        $this->addSql('ALTER TABLE question_comments ADD CONSTRAINT FK_3C9626261E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE questions_comments_flag ADD CONSTRAINT FK_DF6584909FBDE29B FOREIGN KEY (question_comment_id) REFERENCES question_comments (id)');
        $this->addSql('ALTER TABLE question_tags ADD CONSTRAINT FK_315279C91E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_views ADD CONSTRAINT FK_211B8D831E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE doctor_questions ADD CONSTRAINT FK_45CE353E1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_images ADD CONSTRAINT FK_E74291621E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)');
        $this->addSql('ALTER TABLE doctor_replies_votes ADD CONSTRAINT FK_B4AA8EE28A0E4E7F FOREIGN KEY (reply_id) REFERENCES doctor_replies (id)');
         $this->addSql('ALTER TABLE question_bookmarks ADD CONSTRAINT FK_710785DB1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF14A500993 FOREIGN KEY (private_thread_id) REFERENCES private_thread (id)');
        $this->addSql('ALTER TABLE private_thread ADD CONSTRAINT FK_1C0EEA52586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)');
        $this->addSql('ALTER TABLE private_thread ADD CONSTRAINT FK_1C0EEA521E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations CHANGE private_thread_id private_thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE private_thread CHANGE user_info_id user_info_id INT DEFAULT NULL, CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_bookmarks CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_comments CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_tags CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_views CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE questions CHANGE user_info_id user_info_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE questions_comments_flag CHANGE question_comment_id question_comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE questions_comments_votes CHANGE question_comment_id question_comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_info DROP is_enabled');
    }
}
