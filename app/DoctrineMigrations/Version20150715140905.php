<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150715140905 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_users (id INT AUTO_INCREMENT NOT NULL, practo_account_id INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, soft_deleted SMALLINT NOT NULL, UNIQUE INDEX UNIQ_B4A95E134DC86608 (practo_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
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

        $this->addSql('ALTER TABLE admin_user_permission DROP FOREIGN KEY FK_7BFEB8A1FED90CCA');
        $this->addSql('ALTER TABLE admin_user_permission DROP FOREIGN KEY FK_7BFEB8A16352511C');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE admin_users');
        $this->addSql('DROP TABLE admin_user_permission');
    }
}
