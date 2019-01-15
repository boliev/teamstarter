<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180607224608 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("INSERT INTO specialization (id, title) VALUES
          (1, 'Product Owner'),
          (2, 'Backend'),
          (3, 'Frontend'),
          (4, 'Mobile'),
          (5, 'Desktop'),
          (6, 'Design'),
          (7, 'DevOps'),
          (8, 'QA'),
          (9, 'Telecom'),
          (10, 'Management'),
          (11, 'Marketing'),
          (12, 'Sales'),
          (13, 'Analytics'),
          (14, 'Content'),
          (15, 'Musician'),
          (16, 'Screenwriter'),
          (1000, 'Other')"
        );

        $this->addSql("INSERT INTO skill (id, slug, title) VALUES
          (1, 'php', 'PHP'),
          (2, 'java', 'Java'),
          (3, 'javascript', 'JavaScript'),
          (4, 'python', 'Python'),
          (5, 'ruby', 'Ruby'),
          (6, 'mysql', 'MySql'),
          (7, 'mongodb', 'MongoDB'),
          (8, 'symfony', 'Symfony'),
          (9, 'jquery', 'JQuery'),
          (10, 'react', 'React')
          ");
        $this->addSql('ALTER SEQUENCE skill_id_seq RESTART WITH 11');

        $this->addSql("INSERT INTO project_statuses (id, name) VALUES 
            (1, 'Idea only'),
            (2, 'Specifications'),
            (3, 'There is a Demo'),
            (4, 'Project released')
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DELETE FROM user_specializations WHERE user_specializations.specialization_id <= 16');
        $this->addSql('DELETE FROM user_specializations WHERE user_specializations.specialization_id = 1000');
        $this->addSql('DELETE FROM specialization WHERE id <= 16');
        $this->addSql('DELETE FROM specialization WHERE id = 1000');

        $this->addSql('DELETE FROM user_skills WHERE user_skills.skill_id <= 10');
        $this->addSql('DELETE FROM skill WHERE id <= 10');

        $this->addSql('DELETE FROM project_statuses WHERE id <= 4');
    }
}
