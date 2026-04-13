<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260222215119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ETL pipeline, step and workflow tables, and update messenger_messages indexes and column types';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE etl_pipeline (id UUID NOT NULL, workflow_id UUID DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, scheduledAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, startedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finishedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(20) NOT NULL, configuration JSON NOT NULL, input JSON NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_FB3190EF2C7C2CBA ON etl_pipeline (workflow_id)');
            $this->addSql('COMMENT ON COLUMN etl_pipeline.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline.workflow_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline.createdAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline.scheduledAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline.startedAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline.finishedAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE TABLE etl_pipeline_history (id UUID NOT NULL, pipeline_id UUID NOT NULL, status VARCHAR(255) NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_16086583E80B93 ON etl_pipeline_history (pipeline_id)');
            $this->addSql('COMMENT ON COLUMN etl_pipeline_history.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline_history.pipeline_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_pipeline_history.createdAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE TABLE etl_step (id UUID NOT NULL, pipeline_id UUID NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, configuration JSON NOT NULL, "order" INT NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_42863395E80B93 ON etl_step (pipeline_id)');
            $this->addSql('COMMENT ON COLUMN etl_step.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_step.pipeline_id IS \'(DC2Type:uuid)\'');
            $this->addSql('CREATE TABLE etl_step_history (id UUID NOT NULL, step_id UUID NOT NULL, status VARCHAR(255) NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pipelineHistory_id UUID DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_47C368806F83648D ON etl_step_history (pipelineHistory_id)');
            $this->addSql('CREATE INDEX IDX_47C3688073B21E9C ON etl_step_history (step_id)');
            $this->addSql('COMMENT ON COLUMN etl_step_history.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_step_history.step_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_step_history.createdAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_step_history.pipelineHistory_id IS \'(DC2Type:uuid)\'');
            $this->addSql('CREATE TABLE etl_workflow (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, configuration JSON NOT NULL, stepConfiguration JSON NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('COMMENT ON COLUMN etl_workflow.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow.createdAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow.updatedAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('ALTER TABLE etl_pipeline ADD CONSTRAINT FK_FB3190EF2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE etl_pipeline_history ADD CONSTRAINT FK_16086583E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE etl_step ADD CONSTRAINT FK_42863395E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C368806F83648D FOREIGN KEY (pipelineHistory_id) REFERENCES etl_pipeline_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C3688073B21E9C FOREIGN KEY (step_id) REFERENCES etl_step (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('DROP INDEX idx_75ea56e016ba31db');
            $this->addSql('DROP INDEX idx_75ea56e0e3bd61ce');
            $this->addSql('DROP INDEX idx_75ea56e0fb7336f0');
            $this->addSql('ALTER TABLE messenger_messages ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
            $this->addSql('ALTER TABLE messenger_messages ALTER available_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
            $this->addSql('ALTER TABLE messenger_messages ALTER delivered_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
            $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('CREATE TABLE etl_pipeline (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', workflow_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', scheduledAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', startedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', finishedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(20) NOT NULL, configuration JSON NOT NULL, input JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE INDEX IDX_FB3190EF2C7C2CBA ON etl_pipeline (workflow_id)');
            $this->addSql('CREATE TABLE etl_pipeline_history (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', pipeline_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE INDEX IDX_16086583E80B93 ON etl_pipeline_history (pipeline_id)');
            $this->addSql('CREATE TABLE etl_step (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', pipeline_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', code VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, configuration JSON NOT NULL, `order` INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE INDEX IDX_42863395E80B93 ON etl_step (pipeline_id)');
            $this->addSql('CREATE TABLE etl_step_history (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', step_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', pipelineHistory_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE INDEX IDX_47C368806F83648D ON etl_step_history (pipelineHistory_id)');
            $this->addSql('CREATE INDEX IDX_47C3688073B21E9C ON etl_step_history (step_id)');
            $this->addSql('CREATE TABLE etl_workflow (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, configuration JSON NOT NULL, stepConfiguration JSON NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE etl_pipeline ADD CONSTRAINT FK_FB3190EF2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id)');
            $this->addSql('ALTER TABLE etl_pipeline_history ADD CONSTRAINT FK_16086583E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE etl_step ADD CONSTRAINT FK_42863395E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id)');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C368806F83648D FOREIGN KEY (pipelineHistory_id) REFERENCES etl_pipeline_history (id)');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C3688073B21E9C FOREIGN KEY (step_id) REFERENCES etl_step (id) ON DELETE CASCADE');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_pipeline DROP CONSTRAINT FK_FB3190EF2C7C2CBA');
            $this->addSql('ALTER TABLE etl_pipeline_history DROP CONSTRAINT FK_16086583E80B93');
            $this->addSql('ALTER TABLE etl_step DROP CONSTRAINT FK_42863395E80B93');
            $this->addSql('ALTER TABLE etl_step_history DROP CONSTRAINT FK_47C368806F83648D');
            $this->addSql('ALTER TABLE etl_step_history DROP CONSTRAINT FK_47C3688073B21E9C');
            $this->addSql('DROP TABLE etl_pipeline');
            $this->addSql('DROP TABLE etl_pipeline_history');
            $this->addSql('DROP TABLE etl_step');
            $this->addSql('DROP TABLE etl_step_history');
            $this->addSql('DROP TABLE etl_workflow');
            $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750');
            $this->addSql('ALTER TABLE messenger_messages ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
            $this->addSql('ALTER TABLE messenger_messages ALTER available_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
            $this->addSql('ALTER TABLE messenger_messages ALTER delivered_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
            $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS NULL');
            $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS NULL');
            $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS NULL');
            $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');
            $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
            $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_pipeline DROP FOREIGN KEY FK_FB3190EF2C7C2CBA');
            $this->addSql('ALTER TABLE etl_pipeline_history DROP FOREIGN KEY FK_16086583E80B93');
            $this->addSql('ALTER TABLE etl_step DROP FOREIGN KEY FK_42863395E80B93');
            $this->addSql('ALTER TABLE etl_step_history DROP FOREIGN KEY FK_47C368806F83648D');
            $this->addSql('ALTER TABLE etl_step_history DROP FOREIGN KEY FK_47C3688073B21E9C');
            $this->addSql('DROP TABLE etl_pipeline');
            $this->addSql('DROP TABLE etl_pipeline_history');
            $this->addSql('DROP TABLE etl_step');
            $this->addSql('DROP TABLE etl_step_history');
            $this->addSql('DROP TABLE etl_workflow');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }
}
