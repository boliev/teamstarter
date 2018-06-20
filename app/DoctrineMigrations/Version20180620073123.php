<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620073123 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE countries_alias (country VARCHAR(3) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(country, name))');
        $this->addSql('CREATE INDEX IDX_EBFB66CA5373C966 ON countries_alias (country)');
        $this->addSql('ALTER TABLE countries_alias ADD CONSTRAINT FK_EBFB66CA5373C966 FOREIGN KEY (country) REFERENCES country (code) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE countries_alias');
    }
}
