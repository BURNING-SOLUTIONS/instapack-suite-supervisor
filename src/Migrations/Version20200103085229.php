<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103085229 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_privilege DROP FOREIGN KEY FK_C840AAA33E030ACD');
        $this->addSql('DROP INDEX IDX_C840AAA33E030ACD ON core_privilege');
        $this->addSql('ALTER TABLE core_privilege DROP application_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_privilege ADD application_id INT NOT NULL');
        $this->addSql('ALTER TABLE core_privilege ADD CONSTRAINT FK_C840AAA33E030ACD FOREIGN KEY (application_id) REFERENCES core_application (id)');
        $this->addSql('CREATE INDEX IDX_C840AAA33E030ACD ON core_privilege (application_id)');
    }
}
