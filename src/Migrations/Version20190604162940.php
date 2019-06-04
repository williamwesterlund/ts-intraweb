<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190604162940 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news DROP author_id');
        $this->addSql('ALTER TABLE comments DROP news_id, DROP author_id');
        $this->addSql('ALTER TABLE student_reports DROP teacher_id, DROP student_id');
        $this->addSql('ALTER TABLE clients DROP teacher_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE clients ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments ADD news_id INT NOT NULL, ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE news ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE student_reports ADD teacher_id INT NOT NULL, ADD student_id INT NOT NULL');
    }
}
