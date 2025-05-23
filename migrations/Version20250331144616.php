<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331144616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant CHANGE pm_openning_time pm_openning_time JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(32) NOT NULL, ADD last_name VARCHAR(64) NOT NULL, ADD guest_number SMALLINT DEFAULT NULL, ADD allergy VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant CHANGE pm_openning_time pm_openning_time LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE user DROP first_name, DROP last_name, DROP guest_number, DROP allergy');
    }
}
