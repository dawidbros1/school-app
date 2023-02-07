<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207132258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_class DROP INDEX UNIQ_33B1AF8541807E1D, ADD INDEX IDX_33B1AF8541807E1D (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school_class DROP INDEX IDX_33B1AF8541807E1D, ADD UNIQUE INDEX UNIQ_33B1AF8541807E1D (teacher_id)');
    }
}
