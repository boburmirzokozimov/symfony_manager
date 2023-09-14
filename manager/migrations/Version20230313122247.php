<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230313122247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE work_projects_tasks_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_user_networks (id UUID NOT NULL, user_id UUID NOT NULL, network VARCHAR(32) DEFAULT NULL, identity VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7BAFD7B608487BC ON user_user_networks (network)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7BAFD7B6A95E9C4 ON user_user_networks (identity)');
        $this->addSql('CREATE INDEX IDX_D7BAFD7BA76ED395 ON user_user_networks (user_id)');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('CREATE TABLE user_users (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) DEFAULT NULL, new_email VARCHAR(255) DEFAULT NULL, new_email_token VARCHAR(255) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, confirm_token VARCHAR(255) DEFAULT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(255) NOT NULL, reset_token_token VARCHAR(255) DEFAULT NULL, reset_token_expires DATE DEFAULT NULL, name_first VARCHAR(255) NOT NULL, name_last VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1E7927C74 ON user_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB11F043FA9 ON user_users (new_email)');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_token_expires IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE work_members_groups (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN work_members_groups.id IS \'(DC2Type:work_members_group_id)\'');
        $this->addSql('CREATE TABLE work_members_members (id UUID NOT NULL, group_id UUID NOT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, name_first VARCHAR(255) NOT NULL, name_last VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_30039B6DFE54D947 ON work_members_members (group_id)');
        $this->addSql('COMMENT ON COLUMN work_members_members.id IS \'(DC2Type:work_members_member_id)\'');
        $this->addSql('COMMENT ON COLUMN work_members_members.group_id IS \'(DC2Type:work_members_group_id)\'');
        $this->addSql('COMMENT ON COLUMN work_members_members.email IS \'(DC2Type:work_members_member_email)\'');
        $this->addSql('COMMENT ON COLUMN work_members_members.status IS \'(DC2Type:work_members_member_status)\'');
        $this->addSql('CREATE TABLE work_projects_project_departments (id UUID NOT NULL, project_id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F870303A166D1F9C ON work_projects_project_departments (project_id)');
        $this->addSql('COMMENT ON COLUMN work_projects_project_departments.id IS \'(DC2Type:work_projects_department_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_project_departments.project_id IS \'(DC2Type:work_projects_project_id)\'');
        $this->addSql('CREATE TABLE work_projects_project_memberships (id UUID NOT NULL, project_id UUID NOT NULL, member_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6884CF98166D1F9C ON work_projects_project_memberships (project_id)');
        $this->addSql('CREATE INDEX IDX_6884CF987597D3FE ON work_projects_project_memberships (member_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6884CF98166D1F9C7597D3FE ON work_projects_project_memberships (project_id, member_id)');
        $this->addSql('COMMENT ON COLUMN work_projects_project_memberships.project_id IS \'(DC2Type:work_projects_project_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_project_memberships.member_id IS \'(DC2Type:work_members_member_id)\'');
        $this->addSql('CREATE TABLE work_projects_project_membership_departments (membership_id UUID NOT NULL, department_id UUID NOT NULL, PRIMARY KEY(membership_id, department_id))');
        $this->addSql('CREATE INDEX IDX_D94281DD1FB354CD ON work_projects_project_membership_departments (membership_id)');
        $this->addSql('CREATE INDEX IDX_D94281DDAE80F5DF ON work_projects_project_membership_departments (department_id)');
        $this->addSql('COMMENT ON COLUMN work_projects_project_membership_departments.department_id IS \'(DC2Type:work_projects_department_id)\'');
        $this->addSql('CREATE TABLE work_projects_project_membership_roles (membership_id UUID NOT NULL, role_id UUID NOT NULL, PRIMARY KEY(membership_id, role_id))');
        $this->addSql('CREATE INDEX IDX_42102BF81FB354CD ON work_projects_project_membership_roles (membership_id)');
        $this->addSql('CREATE INDEX IDX_42102BF8D60322AC ON work_projects_project_membership_roles (role_id)');
        $this->addSql('COMMENT ON COLUMN work_projects_project_membership_roles.role_id IS \'(DC2Type:work_projects_role_id)\'');
        $this->addSql('CREATE TABLE work_projects_projects (id UUID NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, sort INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN work_projects_projects.id IS \'(DC2Type:work_projects_project_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_projects.status IS \'(DC2Type:work_projects_project_status)\'');
        $this->addSql('CREATE TABLE work_projects_role (id UUID NOT NULL, name VARCHAR(255) NOT NULL, permissions JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29AA4F965E237E06 ON work_projects_role (name)');
        $this->addSql('COMMENT ON COLUMN work_projects_role.id IS \'(DC2Type:work_projects_role_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_role.permissions IS \'(DC2Type:work_projects_role_permissions)\'');
        $this->addSql('CREATE TABLE work_projects_task_files (id UUID NOT NULL, task_id INT NOT NULL, member_id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, info_size INT NOT NULL, info_name VARCHAR(255) NOT NULL, info_path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B8A3E1028DB60186 ON work_projects_task_files (task_id)');
        $this->addSql('CREATE INDEX IDX_B8A3E1027597D3FE ON work_projects_task_files (member_id)');
        $this->addSql('CREATE INDEX IDX_B8A3E102AA9E377A ON work_projects_task_files (date)');
        $this->addSql('COMMENT ON COLUMN work_projects_task_files.id IS \'(DC2Type:work_projects_task_file_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_task_files.task_id IS \'(DC2Type:work_projects_tasks_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_task_files.member_id IS \'(DC2Type:work_members_member_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_task_files.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE work_projects_tasks (id INT NOT NULL, parent_id INT DEFAULT NULL, project_id UUID NOT NULL, author_id UUID NOT NULL, progress SMALLINT NOT NULL, plan_date DATE DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, status VARCHAR(16) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, priority SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E42D1865727ACA70 ON work_projects_tasks (parent_id)');
        $this->addSql('CREATE INDEX IDX_E42D1865166D1F9C ON work_projects_tasks (project_id)');
        $this->addSql('CREATE INDEX IDX_E42D1865F675F31B ON work_projects_tasks (author_id)');
        $this->addSql('CREATE INDEX IDX_E42D1865AA9E377A ON work_projects_tasks (date)');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.id IS \'(DC2Type:work_projects_tasks_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.parent_id IS \'(DC2Type:work_projects_tasks_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.project_id IS \'(DC2Type:work_projects_project_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.author_id IS \'(DC2Type:work_members_member_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.plan_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.start_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.end_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.status IS \'(DC2Type:work_projects_tasks_status)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.type IS \'(DC2Type:work_projects_tasks_type)\'');
        $this->addSql('CREATE TABLE work_projects_tasks_executors (task_id INT NOT NULL, member_id UUID NOT NULL, PRIMARY KEY(task_id, member_id))');
        $this->addSql('CREATE INDEX IDX_6291D08E8DB60186 ON work_projects_tasks_executors (task_id)');
        $this->addSql('CREATE INDEX IDX_6291D08E7597D3FE ON work_projects_tasks_executors (member_id)');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks_executors.task_id IS \'(DC2Type:work_projects_tasks_id)\'');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks_executors.member_id IS \'(DC2Type:work_members_member_id)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE user_user_networks ADD CONSTRAINT FK_D7BAFD7BA76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_members_members ADD CONSTRAINT FK_30039B6DFE54D947 FOREIGN KEY (group_id) REFERENCES work_members_groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_departments ADD CONSTRAINT FK_F870303A166D1F9C FOREIGN KEY (project_id) REFERENCES work_projects_projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_memberships ADD CONSTRAINT FK_6884CF98166D1F9C FOREIGN KEY (project_id) REFERENCES work_projects_projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_memberships ADD CONSTRAINT FK_6884CF987597D3FE FOREIGN KEY (member_id) REFERENCES work_members_members (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_membership_departments ADD CONSTRAINT FK_D94281DD1FB354CD FOREIGN KEY (membership_id) REFERENCES work_projects_project_memberships (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_membership_departments ADD CONSTRAINT FK_D94281DDAE80F5DF FOREIGN KEY (department_id) REFERENCES work_projects_project_departments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_membership_roles ADD CONSTRAINT FK_42102BF81FB354CD FOREIGN KEY (membership_id) REFERENCES work_projects_project_memberships (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_project_membership_roles ADD CONSTRAINT FK_42102BF8D60322AC FOREIGN KEY (role_id) REFERENCES work_projects_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_task_files ADD CONSTRAINT FK_B8A3E1028DB60186 FOREIGN KEY (task_id) REFERENCES work_projects_tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_task_files ADD CONSTRAINT FK_B8A3E1027597D3FE FOREIGN KEY (member_id) REFERENCES work_members_members (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D1865727ACA70 FOREIGN KEY (parent_id) REFERENCES work_projects_tasks (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D1865166D1F9C FOREIGN KEY (project_id) REFERENCES work_projects_projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D1865F675F31B FOREIGN KEY (author_id) REFERENCES work_members_members (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks_executors ADD CONSTRAINT FK_6291D08E8DB60186 FOREIGN KEY (task_id) REFERENCES work_projects_tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_projects_tasks_executors ADD CONSTRAINT FK_6291D08E7597D3FE FOREIGN KEY (member_id) REFERENCES work_members_members (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE work_projects_tasks_seq CASCADE');
        $this->addSql('ALTER TABLE user_user_networks DROP CONSTRAINT FK_D7BAFD7BA76ED395');
        $this->addSql('ALTER TABLE work_members_members DROP CONSTRAINT FK_30039B6DFE54D947');
        $this->addSql('ALTER TABLE work_projects_project_departments DROP CONSTRAINT FK_F870303A166D1F9C');
        $this->addSql('ALTER TABLE work_projects_project_memberships DROP CONSTRAINT FK_6884CF98166D1F9C');
        $this->addSql('ALTER TABLE work_projects_project_memberships DROP CONSTRAINT FK_6884CF987597D3FE');
        $this->addSql('ALTER TABLE work_projects_project_membership_departments DROP CONSTRAINT FK_D94281DD1FB354CD');
        $this->addSql('ALTER TABLE work_projects_project_membership_departments DROP CONSTRAINT FK_D94281DDAE80F5DF');
        $this->addSql('ALTER TABLE work_projects_project_membership_roles DROP CONSTRAINT FK_42102BF81FB354CD');
        $this->addSql('ALTER TABLE work_projects_project_membership_roles DROP CONSTRAINT FK_42102BF8D60322AC');
        $this->addSql('ALTER TABLE work_projects_task_files DROP CONSTRAINT FK_B8A3E1028DB60186');
        $this->addSql('ALTER TABLE work_projects_task_files DROP CONSTRAINT FK_B8A3E1027597D3FE');
        $this->addSql('ALTER TABLE work_projects_tasks DROP CONSTRAINT FK_E42D1865727ACA70');
        $this->addSql('ALTER TABLE work_projects_tasks DROP CONSTRAINT FK_E42D1865166D1F9C');
        $this->addSql('ALTER TABLE work_projects_tasks DROP CONSTRAINT FK_E42D1865F675F31B');
        $this->addSql('ALTER TABLE work_projects_tasks_executors DROP CONSTRAINT FK_6291D08E8DB60186');
        $this->addSql('ALTER TABLE work_projects_tasks_executors DROP CONSTRAINT FK_6291D08E7597D3FE');
        $this->addSql('DROP TABLE user_user_networks');
        $this->addSql('DROP TABLE user_users');
        $this->addSql('DROP TABLE work_members_groups');
        $this->addSql('DROP TABLE work_members_members');
        $this->addSql('DROP TABLE work_projects_project_departments');
        $this->addSql('DROP TABLE work_projects_project_memberships');
        $this->addSql('DROP TABLE work_projects_project_membership_departments');
        $this->addSql('DROP TABLE work_projects_project_membership_roles');
        $this->addSql('DROP TABLE work_projects_projects');
        $this->addSql('DROP TABLE work_projects_role');
        $this->addSql('DROP TABLE work_projects_task_files');
        $this->addSql('DROP TABLE work_projects_tasks');
        $this->addSql('DROP TABLE work_projects_tasks_executors');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
