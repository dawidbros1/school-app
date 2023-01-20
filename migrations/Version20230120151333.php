<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230120151333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add two field to table user: [ first_name, last_name ]';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP first_name');
        $this->addSql('ALTER TABLE user DROP last_name');
    }
}