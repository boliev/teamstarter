<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181106065635 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE messages (id INT NOT NULL, from_id INT DEFAULT NULL, to_id INT DEFAULT NULL, offer_id INT DEFAULT NULL, message TEXT DEFAULT \'\' NOT NULL, status VARCHAR(255) DEFAULT \'New\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB021E9678CED90B ON messages (from_id)');
        $this->addSql('CREATE INDEX IDX_DB021E9630354A65 ON messages (to_id)');
        $this->addSql('CREATE INDEX IDX_DB021E9653C674EE ON messages (offer_id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9678CED90B FOREIGN KEY (from_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9630354A65 FOREIGN KEY (to_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9653C674EE FOREIGN KEY (offer_id) REFERENCES offers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE messages_id_seq CASCADE');
        $this->addSql('DROP TABLE messages');
    }
}
