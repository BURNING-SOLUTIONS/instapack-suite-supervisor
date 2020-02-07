<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103185247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE core_oauth (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, access_code VARCHAR(255) NOT NULL, expiration DATETIME NOT NULL, INDEX IDX_525B59243E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_oauth ADD CONSTRAINT FK_525B59243E030ACD FOREIGN KEY (application_id) REFERENCES core_application (id)');
        $this->addSql('DROP TABLE oauth');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, access_code VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, expiration DATETIME NOT NULL, INDEX IDX_4DA78C43E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE oauth ADD CONSTRAINT FK_4DA78C43E030ACD FOREIGN KEY (application_id) REFERENCES core_application (id)');
        $this->addSql('DROP TABLE core_oauth');
    }
}
