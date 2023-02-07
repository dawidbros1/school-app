<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207105423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE school_class (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, status_id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_33B1AF8541807E1D (teacher_id), UNIQUE INDEX UNIQ_33B1AF856BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school_class_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE school_class ADD CONSTRAINT FK_33B1AF8541807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE school_class ADD CONSTRAINT FK_33B1AF856BF700BD FOREIGN KEY (status_id) REFERENCES school_class_status (id)');
        $this->addSql('ALTER TABLE teacher ADD class_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5EA000B10 FOREIGN KEY (class_id) REFERENCES school_class (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5EA000B10 ON teacher (class_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher DROP FOREIGN KEY FK_B0F6A6D5EA000B10');
        $this->addSql('ALTER TABLE school_class DROP FOREIGN KEY FK_33B1AF8541807E1D');
        $this->addSql('ALTER TABLE school_class DROP FOREIGN KEY FK_33B1AF856BF700BD');
        $this->addSql('DROP TABLE school_class');
        $this->addSql('DROP TABLE school_class_status');
        $this->addSql('DROP INDEX UNIQ_B0F6A6D5EA000B10 ON teacher');
        $this->addSql('ALTER TABLE teacher DROP class_id');
    }
}