<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181010212821 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE offers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE offers (id INT NOT NULL, from_id INT DEFAULT NULL, project_id INT DEFAULT NULL, project_open_role_id INT DEFAULT NULL, to_id INT DEFAULT NULL, status VARCHAR(255) DEFAULT \'New\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA46042778CED90B ON offers (from_id)');
        $this->addSql('CREATE INDEX IDX_DA460427166D1F9C ON offers (project_id)');
        $this->addSql('CREATE INDEX IDX_DA46042791900A3C ON offers (project_open_role_id)');
        $this->addSql('CREATE INDEX IDX_DA46042730354A65 ON offers (to_id)');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA46042778CED90B FOREIGN KEY (from_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA460427166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA46042791900A3C FOREIGN KEY (project_open_role_id) REFERENCES project_open_roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA46042730354A65 FOREIGN KEY (to_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE offers_id_seq CASCADE');
        $this->addSql('DROP TABLE offers');
    }
}
