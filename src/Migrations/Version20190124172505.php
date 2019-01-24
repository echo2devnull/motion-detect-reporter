<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190124172505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, device VARCHAR(255) NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX device_date_start_idx ON notification (device, date_start)');
        $this->addSql('CREATE INDEX date_end_idx ON notification (date_end)');
        $this->addSql('CREATE UNIQUE INDEX device_date_start_date_end_udx ON notification (device, date_start, date_end)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE notification');
    }
}
