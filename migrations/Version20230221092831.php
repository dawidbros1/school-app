<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221092831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE schedule_template (id INT AUTO_INCREMENT NOT NULL, class_id INT DEFAULT NULL, class_time_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, day VARCHAR(64) NOT NULL, place VARCHAR(64) NOT NULL, INDEX IDX_2B8BF6B5EA000B10 (class_id), INDEX IDX_2B8BF6B59B9FBAC7 (class_time_id), INDEX IDX_2B8BF6B541807E1D (teacher_id), INDEX IDX_2B8BF6B523EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B5EA000B10 FOREIGN KEY (class_id) REFERENCES school_class (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B59B9FBAC7 FOREIGN KEY (class_time_id) REFERENCES class_times (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B541807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B523EDC87 FOREIGN KEY (subject_id) REFERENCES school_subject (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B5EA000B10');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B59B9FBAC7');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B541807E1D');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B523EDC87');
        $this->addSql('DROP TABLE schedule_template');
    }
}
