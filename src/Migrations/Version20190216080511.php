<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216080511 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE promo_codes (code VARCHAR(255) NOT NULL, description TEXT NOT NULL, free_pro_months INT DEFAULT 0 NOT NULL, PRIMARY KEY(code))');
        $this->addSql('ALTER TABLE users ADD promoCode VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DC4AC138 FOREIGN KEY (promoCode) REFERENCES promo_codes (code) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1483A5E9DC4AC138 ON users (promoCode)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9DC4AC138');
        $this->addSql('DROP TABLE promo_codes');
        $this->addSql('DROP INDEX IDX_1483A5E9DC4AC138');
        $this->addSql('ALTER TABLE users DROP promoCode');
    }
}
