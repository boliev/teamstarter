<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180111410922 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user MODIFY like_to_do LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user MODIFY expectation LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user MODIFY experience LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user MODIFY about LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user MODIFY about_form_skipped DATETIME DEFAULT "1970-01-01 01:01:01"');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user MODIFY like_to_do LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user MODIFY expectation LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user MODIFY experience LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user MODIFY about LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user MODIFY about_form_skipped DATETIME NOT NULL');
    }
}
