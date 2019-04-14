<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190411054136 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pgcrypto');
        $this->addSql('ALTER TABLE users ADD unsubscribe_hash UUID NOT NULL DEFAULT gen_random_uuid()');
        $this->addSql('COMMENT ON COLUMN users.unsubscribe_hash IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9B62FD5C2 ON users (unsubscribe_hash)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_1483A5E9B62FD5C2');
        $this->addSql('ALTER TABLE users DROP unsubscribe_hash');
    }
}
