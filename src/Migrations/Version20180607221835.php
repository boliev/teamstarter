<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180607221835 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE projects_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_statuses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_demo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_screens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_specializations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE skill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_open_roles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_open_role_skills_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_skills_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE specialization_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE projects (id INT NOT NULL, user_id INT DEFAULT NULL, status_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, progress_status VARCHAR(255) DEFAULT \'Unfinished\' NOT NULL, description TEXT DEFAULT NULL, mission VARCHAR(255) DEFAULT NULL, country VARCHAR(3) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, is_deleted BOOLEAN DEFAULT \'false\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C93B3A4A76ED395 ON projects (user_id)');
        $this->addSql('CREATE INDEX IDX_5C93B3A46BF700BD ON projects (status_id)');
        $this->addSql('CREATE TABLE project_statuses (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE project_demo (id INT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EFD317B8166D1F9C ON project_demo (project_id)');
        $this->addSql('CREATE TABLE project_screens (id INT NOT NULL, project_id INT DEFAULT NULL, screenshot VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AB16BFA3166D1F9C ON project_screens (project_id)');
        $this->addSql('CREATE TABLE user_specializations (id INT NOT NULL, user_id INT DEFAULT NULL, specialization_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A42BF230A76ED395 ON user_specializations (user_id)');
        $this->addSql('CREATE INDEX IDX_A42BF230FA846217 ON user_specializations (specialization_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, facebook_id VARCHAR(255) DEFAULT NULL, facebook_access_token VARCHAR(255) DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, google_access_token VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, password_auto_generated BOOLEAN DEFAULT \'false\' NOT NULL, github_access_token VARCHAR(255) DEFAULT NULL, github_id VARCHAR(255) DEFAULT NULL, country VARCHAR(3) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, like_to_do TEXT DEFAULT NULL, expectation TEXT DEFAULT NULL, experience TEXT DEFAULT NULL, about TEXT DEFAULT NULL, about_form_skipped TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE skill (id INT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE project_open_roles (id INT NOT NULL, project_id INT DEFAULT NULL, specialization_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, vacant BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52575F3166D1F9C ON project_open_roles (project_id)');
        $this->addSql('CREATE INDEX IDX_E52575F3FA846217 ON project_open_roles (specialization_id)');
        $this->addSql('CREATE TABLE project_open_role_skills (id INT NOT NULL, open_role_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_47DD0CD7BB37EE5F ON project_open_role_skills (open_role_id)');
        $this->addSql('CREATE INDEX IDX_47DD0CD75585C142 ON project_open_role_skills (skill_id)');
        $this->addSql('CREATE TABLE user_skills (id INT NOT NULL, user_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B0630D4DA76ED395 ON user_skills (user_id)');
        $this->addSql('CREATE INDEX IDX_B0630D4D5585C142 ON user_skills (skill_id)');
        $this->addSql('CREATE TABLE specialization (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A46BF700BD FOREIGN KEY (status_id) REFERENCES project_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_demo ADD CONSTRAINT FK_EFD317B8166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_screens ADD CONSTRAINT FK_AB16BFA3166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_specializations ADD CONSTRAINT FK_A42BF230A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_specializations ADD CONSTRAINT FK_A42BF230FA846217 FOREIGN KEY (specialization_id) REFERENCES specialization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_open_roles ADD CONSTRAINT FK_E52575F3166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_open_roles ADD CONSTRAINT FK_E52575F3FA846217 FOREIGN KEY (specialization_id) REFERENCES specialization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_open_role_skills ADD CONSTRAINT FK_47DD0CD7BB37EE5F FOREIGN KEY (open_role_id) REFERENCES project_open_roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_open_role_skills ADD CONSTRAINT FK_47DD0CD75585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT FK_B0630D4DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT FK_B0630D4D5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_demo DROP CONSTRAINT FK_EFD317B8166D1F9C');
        $this->addSql('ALTER TABLE project_screens DROP CONSTRAINT FK_AB16BFA3166D1F9C');
        $this->addSql('ALTER TABLE project_open_roles DROP CONSTRAINT FK_E52575F3166D1F9C');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A46BF700BD');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A4A76ED395');
        $this->addSql('ALTER TABLE user_specializations DROP CONSTRAINT FK_A42BF230A76ED395');
        $this->addSql('ALTER TABLE user_skills DROP CONSTRAINT FK_B0630D4DA76ED395');
        $this->addSql('ALTER TABLE project_open_role_skills DROP CONSTRAINT FK_47DD0CD75585C142');
        $this->addSql('ALTER TABLE user_skills DROP CONSTRAINT FK_B0630D4D5585C142');
        $this->addSql('ALTER TABLE project_open_role_skills DROP CONSTRAINT FK_47DD0CD7BB37EE5F');
        $this->addSql('ALTER TABLE user_specializations DROP CONSTRAINT FK_A42BF230FA846217');
        $this->addSql('ALTER TABLE project_open_roles DROP CONSTRAINT FK_E52575F3FA846217');
        $this->addSql('DROP SEQUENCE projects_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_statuses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_demo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_screens_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_specializations_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE skill_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_open_roles_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_open_role_skills_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_skills_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE specialization_id_seq CASCADE');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE project_statuses');
        $this->addSql('DROP TABLE project_demo');
        $this->addSql('DROP TABLE project_screens');
        $this->addSql('DROP TABLE user_specializations');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE project_open_roles');
        $this->addSql('DROP TABLE project_open_role_skills');
        $this->addSql('DROP TABLE user_skills');
        $this->addSql('DROP TABLE specialization');
    }
}
