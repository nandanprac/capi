<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150723085403 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_questions CHANGE question_id question_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions CHANGE subject subject LONGTEXT DEFAULT NULL, CHANGE text text LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE private_thread CHANGE subject subject LONGTEXT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_questions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE private_thread CHANGE subject subject VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE questions CHANGE subject subject VARCHAR(32) DEFAULT NULL, CHANGE text text VARCHAR(360) NOT NULL');
    }
}
