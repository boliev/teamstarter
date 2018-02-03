<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180203170443 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_demo (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_EFD317B8166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_screens (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, INDEX IDX_AB16BFA3166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_progress (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, progress_id INT DEFAULT NULL, status_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, mission VARCHAR(255) DEFAULT NULL, country VARCHAR(3) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, is_deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_5C93B3A443DB87C9 (progress_id), INDEX IDX_5C93B3A46BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_docs (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_68C6E3AF166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_demo ADD CONSTRAINT FK_EFD317B8166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_screens ADD CONSTRAINT FK_AB16BFA3166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A443DB87C9 FOREIGN KEY (progress_id) REFERENCES project_progress (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A46BF700BD FOREIGN KEY (status_id) REFERENCES project_statuses (id)');
        $this->addSql('ALTER TABLE project_docs ADD CONSTRAINT FK_68C6E3AF166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A46BF700BD');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A443DB87C9');
        $this->addSql('ALTER TABLE project_demo DROP FOREIGN KEY FK_EFD317B8166D1F9C');
        $this->addSql('ALTER TABLE project_screens DROP FOREIGN KEY FK_AB16BFA3166D1F9C');
        $this->addSql('ALTER TABLE project_docs DROP FOREIGN KEY FK_68C6E3AF166D1F9C');
        $this->addSql('DROP TABLE project_statuses');
        $this->addSql('DROP TABLE project_demo');
        $this->addSql('DROP TABLE project_screens');
        $this->addSql('DROP TABLE project_progress');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE project_docs');
    }
}
