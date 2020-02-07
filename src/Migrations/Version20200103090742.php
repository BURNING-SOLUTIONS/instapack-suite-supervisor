<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103090742 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE core_permission (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, application_id INT DEFAULT NULL, privilege_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, available TINYINT(1) DEFAULT NULL, INDEX IDX_DC05164BD60322AC (role_id), INDEX IDX_DC05164B3E030ACD (application_id), INDEX IDX_DC05164B32FB8AEA (privilege_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_permission ADD CONSTRAINT FK_DC05164BD60322AC FOREIGN KEY (role_id) REFERENCES core_role (id)');
        $this->addSql('ALTER TABLE core_permission ADD CONSTRAINT FK_DC05164B3E030ACD FOREIGN KEY (application_id) REFERENCES core_application (id)');
        $this->addSql('ALTER TABLE core_permission ADD CONSTRAINT FK_DC05164B32FB8AEA FOREIGN KEY (privilege_id) REFERENCES core_privilege (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE core_permission');
    }
}
