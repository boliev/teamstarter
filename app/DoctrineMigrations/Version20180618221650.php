<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180618221650 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $regionBundle = \Symfony\Component\Intl\Intl::getRegionBundle();
        $countries = $regionBundle->getCountryNames();

        foreach ($countries as $code => $name) {
            $this->addSql(sprintf("INSERT INTO country (code, name)values ('%s', '%s')", $code, $name));
        }
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A45373C966 FOREIGN KEY (country) REFERENCES country (code) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A45373C966');
        $this->addSql('TRUNCATE country');
    }
}
