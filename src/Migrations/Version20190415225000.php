<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190415225000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("INSERT INTO achievements (id, name, created_at, updated_at) VALUES (1,'junior_comments', NOW(), NOW())");
        $this->addSql("INSERT INTO achievements (id, name, created_at, updated_at) VALUES (2,'middle_comments', NOW(), NOW())");
        $this->addSql("INSERT INTO achievements (id, name, created_at, updated_at) VALUES (3,'senior_comments', NOW(), NOW())");
        $this->addSql("INSERT INTO achievements (id, name, created_at, updated_at) VALUES (4,'entrepreneur', NOW(), NOW())");
        $this->addSql("INSERT INTO achievements (id, name, created_at, updated_at) VALUES (5,'serial_entrepreneur', NOW(), NOW())");
        $this->addSql("INSERT INTO achievements (id, name, created_at, updated_at) VALUES (6,'proactive', NOW(), NOW())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('TRUNCATE countries_alias');
    }
}
