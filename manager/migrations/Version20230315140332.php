<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315140332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE work_projects_tasks_seq CASCADE');
        $this->addSql('CREATE TABLE comment_comments (id UUID NOT NULL, entity_id VARCHAR(36) NOT NULL, author_id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text VARCHAR(255) NOT NULL, update_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, entity_type VARCHAR(255) NOT NULL, PRIMARY KEY(id, entity_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42DAF52CBF396750 ON comment_comments (id)');
        $this->addSql('CREATE INDEX IDX_42DAF52CAA9E377A ON comment_comments (date)');
        $this->addSql('CREATE INDEX IDX_42DAF52C81257D5DC412EE02 ON comment_comments (entity_id, entity_type)');
        $this->addSql('COMMENT ON COLUMN comment_comments.id IS \'(DC2Type:comment_comment_id)\'');
        $this->addSql('COMMENT ON COLUMN comment_comments.author_id IS \'(DC2Type:comment_comment_author_id)\'');
        $this->addSql('COMMENT ON COLUMN comment_comments.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN comment_comments.update_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE work_projects_tasks_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE comment_comments');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
    }
}
