<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180321225139 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_open_vacancy_skills (id INT AUTO_INCREMENT NOT NULL, vacancy_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, priority INT NOT NULL, INDEX IDX_E2069458433B78C4 (vacancy_id), INDEX IDX_E20694585585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_open_vacancy_skills ADD CONSTRAINT FK_E2069458433B78C4 FOREIGN KEY (vacancy_id) REFERENCES project_open_vacancies (id)');
        $this->addSql('ALTER TABLE project_open_vacancy_skills ADD CONSTRAINT FK_E20694585585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
        $this->addSql('ALTER TABLE project_open_vacancies RENAME INDEX idx_1404bd01166d1f9c TO IDX_DDE23F2A166D1F9C');
        $this->addSql('ALTER TABLE project_open_vacancies RENAME INDEX idx_1404bd01fa846217 TO IDX_DDE23F2AFA846217');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE project_open_vacancy_skills');
        $this->addSql('ALTER TABLE project_open_vacancies RENAME INDEX idx_dde23f2a166d1f9c TO IDX_1404BD01166D1F9C');
        $this->addSql('ALTER TABLE project_open_vacancies RENAME INDEX idx_dde23f2afa846217 TO IDX_1404BD01FA846217');
    }
}
