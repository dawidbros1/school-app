<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228125551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson ADD lesson_status_id INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3FD4EF37 FOREIGN KEY (lesson_status_id) REFERENCES lesson_status (id)');
        $this->addSql('CREATE INDEX IDX_F87474F3FD4EF37 ON lesson (lesson_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3FD4EF37');
        $this->addSql('DROP INDEX IDX_F87474F3FD4EF37 ON lesson');
        $this->addSql('ALTER TABLE lesson DROP lesson_status_id');
    }
}
