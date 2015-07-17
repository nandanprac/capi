<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150708164702 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_notifications DROP FOREIGN KEY FK_5B44780F1E27F6BF');
        $this->addSql('DROP INDEX IDX_5B44780F1E27F6BF ON doctor_notifications');
        $this->addSql('ALTER TABLE doctor_notifications CHANGE question_id doctor_question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_notifications ADD CONSTRAINT FK_5B44780F99184244 FOREIGN KEY (doctor_question_id) REFERENCES doctor_questions (id)');
        $this->addSql('CREATE INDEX IDX_5B44780F99184244 ON doctor_notifications (doctor_question_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_notifications DROP FOREIGN KEY FK_5B44780F99184244');
        $this->addSql('DROP INDEX IDX_5B44780F99184244 ON doctor_notifications');
        $this->addSql('ALTER TABLE doctor_notifications CHANGE doctor_question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_notifications ADD CONSTRAINT FK_5B44780F1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('CREATE INDEX IDX_5B44780F1E27F6BF ON doctor_notifications (question_id)');
    }
}
