<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150714110114 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_user_permission (admin_user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_7BFEB8A16352511C (admin_user_id), INDEX IDX_7BFEB8A1FED90CCA (permission_id), PRIMARY KEY(admin_user_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_user_permission ADD CONSTRAINT FK_7BFEB8A16352511C FOREIGN KEY (admin_user_id) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE admin_user_permission ADD CONSTRAINT FK_7BFEB8A1FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE admin_user_permission');
    }
}
