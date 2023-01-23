<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230120153356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add table roles with data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql("INSERT INTO roles (name, description) VALUES ('ROLE_STUDENT','Uczeń'), ('ROLE_TEACHER','Nauczyciel'), ('ROLE_ADMIN','Administrator')");
    }

    public function down(Schema $schema): void
    {
        // $this->addSql("DELETE FROM roles");
        $this->addSql('DROP TABLE roles');
    }
}