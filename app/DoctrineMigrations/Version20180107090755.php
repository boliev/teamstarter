<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180107090755 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO skill (id, slug, title) VALUES
          (1, "php", "PHP"),
          (2, "java", "Java"),
          (3, "javascript", "JavaScript"),
          (4, "python", "Python"),
          (5, "ruby", "Ruby"),
          (6, "mysql", "MySql"),
          (7, "mongodb", "MongoDB"),
          (8, "symfony", "Symfony"),
          (9, "jquery", "JQuery"),
          (10, "react", "React")
          ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM user_skills WHERE user_skills.skill_id <= 10');
        $this->addSql('DELETE FROM skill WHERE id <= 10');
    }
}
