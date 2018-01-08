<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180107090457 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_specializations (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, specialization_id INT DEFAULT NULL, INDEX IDX_A42BF230A76ED395 (user_id), INDEX IDX_A42BF230FA846217 (specialization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_skills (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, priority INT NOT NULL, INDEX IDX_B0630D4DA76ED395 (user_id), INDEX IDX_B0630D4D5585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialization (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_specializations ADD CONSTRAINT FK_A42BF230A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_specializations ADD CONSTRAINT FK_A42BF230FA846217 FOREIGN KEY (specialization_id) REFERENCES specialization (id)');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT FK_B0630D4DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT FK_B0630D4D5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_skills DROP FOREIGN KEY FK_B0630D4D5585C142');
        $this->addSql('ALTER TABLE user_specializations DROP FOREIGN KEY FK_A42BF230FA846217');
        $this->addSql('DROP TABLE user_specializations');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE user_skills');
        $this->addSql('DROP TABLE specialization');
    }
}
