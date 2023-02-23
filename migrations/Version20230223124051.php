<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223124051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, class_id INT DEFAULT NULL, lesson_time_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, date DATE NOT NULL, place VARCHAR(64) NOT NULL, INDEX IDX_F87474F3EA000B10 (class_id), INDEX IDX_F87474F3AB968F9C (lesson_time_id), INDEX IDX_F87474F341807E1D (teacher_id), INDEX IDX_F87474F323EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_template (id INT AUTO_INCREMENT NOT NULL, class_id INT DEFAULT NULL, lesson_time_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, day VARCHAR(64) NOT NULL, place VARCHAR(64) NOT NULL, INDEX IDX_DF27559AEA000B10 (class_id), INDEX IDX_DF27559AAB968F9C (lesson_time_id), INDEX IDX_DF27559A41807E1D (teacher_id), INDEX IDX_DF27559A23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_time (id INT AUTO_INCREMENT NOT NULL, from_time TIME NOT NULL, to_time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3EA000B10 FOREIGN KEY (class_id) REFERENCES school_class (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3AB968F9C FOREIGN KEY (lesson_time_id) REFERENCES lesson_time (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F323EDC87 FOREIGN KEY (subject_id) REFERENCES school_subject (id)');
        $this->addSql('ALTER TABLE lesson_template ADD CONSTRAINT FK_DF27559AEA000B10 FOREIGN KEY (class_id) REFERENCES school_class (id)');
        $this->addSql('ALTER TABLE lesson_template ADD CONSTRAINT FK_DF27559AAB968F9C FOREIGN KEY (lesson_time_id) REFERENCES lesson_time (id)');
        $this->addSql('ALTER TABLE lesson_template ADD CONSTRAINT FK_DF27559A41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE lesson_template ADD CONSTRAINT FK_DF27559A23EDC87 FOREIGN KEY (subject_id) REFERENCES school_subject (id)');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB9B9FBAC7');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB23EDC87');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBEA000B10');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB41807E1D');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B541807E1D');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B59B9FBAC7');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B523EDC87');
        $this->addSql('ALTER TABLE schedule_template DROP FOREIGN KEY FK_2B8BF6B5EA000B10');
        $this->addSql('DROP TABLE class_times');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE schedule_template');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE class_times (id INT AUTO_INCREMENT NOT NULL, from_time TIME NOT NULL, to_time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, class_id INT DEFAULT NULL, class_time_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, place VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date DATE NOT NULL, INDEX IDX_5A3811FB41807E1D (teacher_id), INDEX IDX_5A3811FB23EDC87 (subject_id), INDEX IDX_5A3811FBEA000B10 (class_id), INDEX IDX_5A3811FB9B9FBAC7 (class_time_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE schedule_template (id INT AUTO_INCREMENT NOT NULL, class_id INT DEFAULT NULL, class_time_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, day VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, place VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2B8BF6B523EDC87 (subject_id), INDEX IDX_2B8BF6B5EA000B10 (class_id), INDEX IDX_2B8BF6B59B9FBAC7 (class_time_id), INDEX IDX_2B8BF6B541807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB9B9FBAC7 FOREIGN KEY (class_time_id) REFERENCES class_times (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB23EDC87 FOREIGN KEY (subject_id) REFERENCES school_subject (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBEA000B10 FOREIGN KEY (class_id) REFERENCES school_class (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B541807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B59B9FBAC7 FOREIGN KEY (class_time_id) REFERENCES class_times (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B523EDC87 FOREIGN KEY (subject_id) REFERENCES school_subject (id)');
        $this->addSql('ALTER TABLE schedule_template ADD CONSTRAINT FK_2B8BF6B5EA000B10 FOREIGN KEY (class_id) REFERENCES school_class (id)');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3EA000B10');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3AB968F9C');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F341807E1D');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F323EDC87');
        $this->addSql('ALTER TABLE lesson_template DROP FOREIGN KEY FK_DF27559AEA000B10');
        $this->addSql('ALTER TABLE lesson_template DROP FOREIGN KEY FK_DF27559AAB968F9C');
        $this->addSql('ALTER TABLE lesson_template DROP FOREIGN KEY FK_DF27559A41807E1D');
        $this->addSql('ALTER TABLE lesson_template DROP FOREIGN KEY FK_DF27559A23EDC87');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_template');
        $this->addSql('DROP TABLE lesson_time');
    }
}
