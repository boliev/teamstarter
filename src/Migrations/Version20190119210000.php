<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190119210000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE country ADD ru VARCHAR(255) DEFAULT \'\'');
        $this->addSql("DELETE FROM country WHERE code not in ('RU', 'AM', 'BY', 'CA', 'DE', 'KZ', 'UA', 'US', 'GB', 'UZ')");
        $this->addSql("UPDATE country SET ru = 'Россия' WHERE code = 'RU'");
        $this->addSql("UPDATE country SET ru = 'Армения' WHERE code = 'AM'");
        $this->addSql("UPDATE country SET ru = 'Беларусь' WHERE code = 'BY'");
        $this->addSql("UPDATE country SET ru = 'Канада' WHERE code = 'CA'");
        $this->addSql("UPDATE country SET ru = 'Германия' WHERE code = 'DE'");
        $this->addSql("UPDATE country SET ru = 'Казахстан' WHERE code = 'KZ'");
        $this->addSql("UPDATE country SET ru = 'Украина' WHERE code = 'UA'");
        $this->addSql("UPDATE country SET ru = 'Соединенные Штаты' WHERE code = 'US'");
        $this->addSql("UPDATE country SET ru = 'Соединенное Королевство' WHERE code = 'GB'");
        $this->addSql("UPDATE country SET ru = 'Узбекистан' WHERE code = 'UZ'");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE country DROP ru');
    }
}
