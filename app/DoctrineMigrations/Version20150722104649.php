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

        $this->addSql('ALTER TABLE question_views CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE conversations CHANGE private_thread_id private_thread_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_info ADD is_enabled TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE questions_comments_votes CHANGE question_comment_id question_comment_id INT NOT NULL');
        $this->addSql('ALTER TABLE question_tags CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions_comments_flag CHANGE question_comment_id question_comment_id INT NOT NULL');
        $this->addSql('ALTER TABLE question_bookmarks CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions CHANGE user_info_id user_info_id INT NOT NULL');
        $this->addSql('ALTER TABLE question_comments CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_thread CHANGE question_id question_id INT NOT NULL, CHANGE user_info_id user_info_id INT NOT NULL');
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
