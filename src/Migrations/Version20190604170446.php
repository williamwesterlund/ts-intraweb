<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190604170446 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_post ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE news_post ADD CONSTRAINT FK_8F579A06F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8F579A06F675F31B ON news_post (author_id)');
        $this->addSql('ALTER TABLE comment ADD news_post_id INT NOT NULL, ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1C6D1FCA FOREIGN KEY (news_post_id) REFERENCES news_post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9474526C1C6D1FCA ON comment (news_post_id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('ALTER TABLE student_report ADD teacher_id INT NOT NULL, ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE student_report ADD CONSTRAINT FK_4A19AF0441807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_report ADD CONSTRAINT FK_4A19AF0419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_4A19AF0441807E1D ON student_report (teacher_id)');
        $this->addSql('CREATE INDEX IDX_4A19AF0419EB6921 ON student_report (client_id)');
        $this->addSql('ALTER TABLE client ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C744045541807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C744045541807E1D ON client (teacher_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C744045541807E1D');
        $this->addSql('DROP INDEX IDX_C744045541807E1D ON client');
        $this->addSql('ALTER TABLE client DROP teacher_id');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C1C6D1FCA');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('DROP INDEX IDX_9474526C1C6D1FCA ON comment');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B ON comment');
        $this->addSql('ALTER TABLE comment DROP news_post_id, DROP author_id');
        $this->addSql('ALTER TABLE news_post DROP FOREIGN KEY FK_8F579A06F675F31B');
        $this->addSql('DROP INDEX IDX_8F579A06F675F31B ON news_post');
        $this->addSql('ALTER TABLE news_post DROP author_id');
        $this->addSql('ALTER TABLE student_report DROP FOREIGN KEY FK_4A19AF0441807E1D');
        $this->addSql('ALTER TABLE student_report DROP FOREIGN KEY FK_4A19AF0419EB6921');
        $this->addSql('DROP INDEX IDX_4A19AF0441807E1D ON student_report');
        $this->addSql('DROP INDEX IDX_4A19AF0419EB6921 ON student_report');
        $this->addSql('ALTER TABLE student_report DROP teacher_id, DROP client_id');
    }
}
