<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180107090555 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO specialization (id, title) VALUES
          (1, "Product Owner"),
          (2, "Backend"),
          (3, "Frontend"),
          (4, "Mobile"),
          (5, "Desktop"),
          (6, "Design"),
          (7, "DevOps"),
          (8, "QA"),
          (9, "Telecom"),
          (10, "Management"),
          (11, "Marketing"),
          (12, "Sales"),
          (13, "Analytics"),
          (14, "Content"),
          (15, "Musician"),
          (16, "Screenwriter"),
          (1000, "Other")'
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM user_specializations WHERE user_specializations.specialization_id <= 15');
        $this->addSql('DELETE FROM specialization WHERE id <= 15');
    }
}
