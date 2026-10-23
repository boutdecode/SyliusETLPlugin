<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Moves the ETL tables to the schema of boutdecode/etl-bundle (camelCase columns, string GUID identifiers,
 * step histories holding the step code and name). Creates them on a fresh database, converts the data of the
 * former plugin tables otherwise. Only touches etl_* tables.
 */
final class Version20261023000000 extends AbstractMigration
{
    private const TABLES = [
        'workflow',
        'step',
        'planned_task',
        'pipeline',
        'pipeline_history',
        'step_history',
        'workflow_execution_statistic',
        'workflow_statistic',
    ];

    public function getDescription(): string
    {
        return 'Align the ETL tables with the schema of boutdecode/etl-bundle';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();
        $postgres = $platform instanceof PostgreSQLPlatform;
        $this->abortIf(!$postgres && !$platform instanceof MySQLPlatform && !$platform instanceof MariaDBPlatform, sprintf('Unsupported database platform: %s', $platform::class));

        $legacy = $schema->hasTable('etl_workflow') && $schema->getTable('etl_workflow')->hasColumn('created_at');

        if ($legacy) {
            $this->detachLegacyTables($postgres);
        } elseif ($schema->hasTable('etl_workflow')) {
            // Already on the schema of etl-bundle (created by doctrine:schema or a diff).
            return;
        }

        foreach ($postgres ? $this->postgresTables() : $this->mysqlTables() as $sql) {
            $this->addSql($sql);
        }

        if ($legacy) {
            $this->copyLegacyData($platform);
        }

        foreach ($postgres ? $this->postgresConstraints() : $this->mysqlConstraints() as $sql) {
            $this->addSql($sql);
        }

        if ($legacy) {
            foreach (array_reverse(self::TABLES) as $table) {
                $this->addSql(sprintf('DROP TABLE etl_legacy_%s', $table));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('The former plugin schema cannot be restored.');
    }

    private function detachLegacyTables(bool $postgres): void
    {
        $constraints = [
            'etl_pipeline' => ['FK_FB3190EF2C7C2CBA'],
            'etl_pipeline_history' => ['FK_16086583E80B93'],
            'etl_planned_task' => ['FK_39A2289C2C7C2CBA', 'FK_39A2289CE80B93'],
            'etl_step' => ['FK_42863395E80B93'],
            'etl_step_history' => ['FK_47C368806F83648D', 'FK_47C3688073B21E9C'],
            'etl_workflow_execution_statistic' => ['FK_etl_workflow_execution_statistic_workflow'],
            'etl_workflow_statistic' => ['FK_etl_workflow_statistic_workflow'],
        ];

        foreach ($constraints as $table => $names) {
            foreach ($names as $name) {
                $this->addSql($postgres
                    ? sprintf('ALTER TABLE %s DROP CONSTRAINT IF EXISTS %s', $table, $name)
                    : sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $name));
            }
        }

        foreach (self::TABLES as $table) {
            $this->addSql(sprintf('ALTER TABLE etl_%s RENAME TO etl_legacy_%s', $table, $table));
        }

        if ($postgres) {
            // Index names are shared by the whole schema, free those the new tables reuse.
            foreach (self::TABLES as $table) {
                $this->addSql(sprintf('ALTER INDEX IF EXISTS etl_%s_pkey RENAME TO etl_legacy_%s_pkey', $table, $table));
            }

            foreach (['IDX_FB3190EF2C7C2CBA', 'IDX_16086583E80B93', 'IDX_39A2289C2C7C2CBA', 'IDX_39A2289CE80B93', 'IDX_42863395E80B93', 'IDX_47C368806F83648D', 'IDX_47C3688073B21E9C', 'IDX_workflow_execution_statistic_workflow', 'IDX_workflow_execution_statistic_started_at', 'UNIQ_workflow_statistic_workflow'] as $index) {
                $this->addSql(sprintf('DROP INDEX IF EXISTS %s', $index));
            }
        }
    }

    private function copyLegacyData(AbstractPlatform $platform): void
    {
        $mysql = !$platform instanceof PostgreSQLPlatform;
        // The former mapping stored identifiers as BINARY(16) on MySQL, the string form is expected now.
        $id = static fn (string $column): string => $mysql
            ? sprintf("LOWER(CONCAT(SUBSTR(HEX(%1\$s), 1, 8), '-', SUBSTR(HEX(%1\$s), 9, 4), '-', SUBSTR(HEX(%1\$s), 13, 4), '-', SUBSTR(HEX(%1\$s), 17, 4), '-', SUBSTR(HEX(%1\$s), 21)))", $column)
            : $column;
        $order = $mysql ? '`order`' : '"order"';

        $this->addSql(sprintf(
            'INSERT INTO etl_workflow (id, name, description, stepConfiguration, configuration, notifyOnSuccess, notifyOnFailure, notificationProviders, createdAt, updatedAt) SELECT %s, name, description, step_configuration, configuration, notify_on_success, notify_on_failure, notification_providers, created_at, updated_at FROM etl_legacy_workflow',
            $id('id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_pipeline (id, name, configuration, input, status, createdAt, scheduledAt, startedAt, finishedAt, workflow_id) SELECT %s, name, configuration, input, status, created_at, scheduled_at, started_at, finished_at, %s FROM etl_legacy_pipeline',
            $id('id'),
            $id('workflow_id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_step (id, name, code, configuration, sort_order, pipeline_id) SELECT %s, name, code, configuration, %s, %s FROM etl_legacy_step',
            $id('id'),
            $order,
            $id('pipeline_id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_planned_task (id, name, enabled, schedule, input, configuration, createdAt, updatedAt, workflow_id, pipeline_id) SELECT %s, name, enabled, schedule, input, configuration, created_at, updated_at, %s, %s FROM etl_legacy_planned_task',
            $id('id'),
            $id('workflow_id'),
            $id('pipeline_id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_pipeline_history (id, status, createdAt, input, result, pipeline_id) SELECT %s, status, created_at, input, result, %s FROM etl_legacy_pipeline_history',
            $id('id'),
            $id('pipeline_id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_step_history (id, stepCode, stepName, status, createdAt, input, result, pipeline_history_id) SELECT %s, COALESCE(s.code, \'\'), COALESCE(s.name, s.code, \'\'), h.status, h.created_at, h.input, h.result, %s FROM etl_legacy_step_history h LEFT JOIN etl_legacy_step s ON s.id = h.step_id',
            $id('h.id'),
            $id('h.pipeline_history_id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_workflow_execution_statistic (id, status, startedAt, finishedAt, workflow_id) SELECT %s, status, started_at, finished_at, %s FROM etl_legacy_workflow_execution_statistic',
            $id('id'),
            $id('workflow_id'),
        ));
        $this->addSql(sprintf(
            'INSERT INTO etl_workflow_statistic (id, totalCount, successCount, failureCount, totalDurationMs, minDurationMs, maxDurationMs, lastRunAt, lastRunStatus, updatedAt, workflow_id) SELECT %s, total_count, success_count, failure_count, total_duration_ms, min_duration_ms, max_duration_ms, last_run_at, last_run_status, updated_at, %s FROM etl_legacy_workflow_statistic',
            $id('id'),
            $id('workflow_id'),
        ));
    }

    /**
     * @return list<string>
     */
    private function mysqlTables(): array
    {
        return [
            'CREATE TABLE etl_workflow (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, stepConfiguration JSON NOT NULL, configuration JSON NOT NULL, notifyOnSuccess TINYINT NOT NULL, notifyOnFailure TINYINT NOT NULL, notificationProviders JSON DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_step (id CHAR(36) NOT NULL, name VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, configuration JSON NOT NULL, sort_order INT NOT NULL, pipeline_id CHAR(36) NOT NULL, INDEX IDX_42863395E80B93 (pipeline_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_planned_task (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT NOT NULL, schedule VARCHAR(255) NOT NULL, input JSON NOT NULL, configuration JSON NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, workflow_id CHAR(36) NOT NULL, pipeline_id CHAR(36) DEFAULT NULL, INDEX IDX_39A2289C2C7C2CBA (workflow_id), INDEX IDX_39A2289CE80B93 (pipeline_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_pipeline (id CHAR(36) NOT NULL, name VARCHAR(255) DEFAULT NULL, configuration JSON NOT NULL, input JSON NOT NULL, status VARCHAR(20) NOT NULL, createdAt DATETIME NOT NULL, scheduledAt DATETIME DEFAULT NULL, startedAt DATETIME DEFAULT NULL, finishedAt DATETIME DEFAULT NULL, workflow_id CHAR(36) DEFAULT NULL, INDEX IDX_FB3190EF2C7C2CBA (workflow_id), INDEX etl_pipeline_status_idx (status), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_pipeline_history (id CHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, createdAt DATETIME NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, pipeline_id CHAR(36) NOT NULL, INDEX IDX_16086583E80B93 (pipeline_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_step_history (id CHAR(36) NOT NULL, stepCode VARCHAR(255) NOT NULL, stepName VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, createdAt DATETIME NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, pipeline_history_id CHAR(36) DEFAULT NULL, INDEX IDX_47C36880EDCFB258 (pipeline_history_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_workflow_execution_statistic (id CHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, startedAt DATETIME NOT NULL, finishedAt DATETIME NOT NULL, workflow_id CHAR(36) NOT NULL, INDEX IDX_E3F9B09A2C7C2CBA (workflow_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
            'CREATE TABLE etl_workflow_statistic (id CHAR(36) NOT NULL, totalCount INT NOT NULL, successCount INT NOT NULL, failureCount INT NOT NULL, totalDurationMs BIGINT NOT NULL, minDurationMs INT DEFAULT NULL, maxDurationMs INT DEFAULT NULL, lastRunAt DATETIME DEFAULT NULL, lastRunStatus VARCHAR(20) DEFAULT NULL, updatedAt DATETIME NOT NULL, workflow_id CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_848B16142C7C2CBA (workflow_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4',
        ];
    }

    /**
     * @return list<string>
     */
    private function mysqlConstraints(): array
    {
        return [
            'ALTER TABLE etl_step ADD CONSTRAINT FK_42863395E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE CASCADE',
            'ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289C2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id)',
            'ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289CE80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE SET NULL',
            'ALTER TABLE etl_pipeline ADD CONSTRAINT FK_FB3190EF2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id)',
            'ALTER TABLE etl_pipeline_history ADD CONSTRAINT FK_16086583E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE CASCADE',
            'ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C36880EDCFB258 FOREIGN KEY (pipeline_history_id) REFERENCES etl_pipeline_history (id) ON DELETE CASCADE',
            'ALTER TABLE etl_workflow_execution_statistic ADD CONSTRAINT FK_E3F9B09A2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE',
            'ALTER TABLE etl_workflow_statistic ADD CONSTRAINT FK_848B16142C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE',
        ];
    }

    /**
     * @return list<string>
     */
    private function postgresTables(): array
    {
        return [
            'CREATE TABLE etl_workflow (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, stepConfiguration JSON NOT NULL, configuration JSON NOT NULL, notifyOnSuccess BOOLEAN NOT NULL, notifyOnFailure BOOLEAN NOT NULL, notificationProviders JSON DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))',
            'CREATE TABLE etl_step (id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, configuration JSON NOT NULL, sort_order INT NOT NULL, pipeline_id UUID NOT NULL, PRIMARY KEY (id))',
            'CREATE INDEX IDX_42863395E80B93 ON etl_step (pipeline_id)',
            'CREATE TABLE etl_planned_task (id UUID NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, schedule VARCHAR(255) NOT NULL, input JSON NOT NULL, configuration JSON NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, workflow_id UUID NOT NULL, pipeline_id UUID DEFAULT NULL, PRIMARY KEY (id))',
            'CREATE INDEX IDX_39A2289C2C7C2CBA ON etl_planned_task (workflow_id)',
            'CREATE INDEX IDX_39A2289CE80B93 ON etl_planned_task (pipeline_id)',
            'CREATE TABLE etl_pipeline (id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, configuration JSON NOT NULL, input JSON NOT NULL, status VARCHAR(20) NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, scheduledAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, startedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finishedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, workflow_id UUID DEFAULT NULL, PRIMARY KEY (id))',
            'CREATE INDEX IDX_FB3190EF2C7C2CBA ON etl_pipeline (workflow_id)',
            'CREATE INDEX etl_pipeline_status_idx ON etl_pipeline (status)',
            'CREATE TABLE etl_pipeline_history (id UUID NOT NULL, status VARCHAR(20) NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, pipeline_id UUID NOT NULL, PRIMARY KEY (id))',
            'CREATE INDEX IDX_16086583E80B93 ON etl_pipeline_history (pipeline_id)',
            'CREATE TABLE etl_step_history (id UUID NOT NULL, stepCode VARCHAR(255) NOT NULL, stepName VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, input JSON DEFAULT NULL, result JSON DEFAULT NULL, pipeline_history_id UUID DEFAULT NULL, PRIMARY KEY (id))',
            'CREATE INDEX IDX_47C36880EDCFB258 ON etl_step_history (pipeline_history_id)',
            'CREATE TABLE etl_workflow_execution_statistic (id UUID NOT NULL, status VARCHAR(20) NOT NULL, startedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finishedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, workflow_id UUID NOT NULL, PRIMARY KEY (id))',
            'CREATE INDEX IDX_E3F9B09A2C7C2CBA ON etl_workflow_execution_statistic (workflow_id)',
            'CREATE TABLE etl_workflow_statistic (id UUID NOT NULL, totalCount INT NOT NULL, successCount INT NOT NULL, failureCount INT NOT NULL, totalDurationMs BIGINT NOT NULL, minDurationMs INT DEFAULT NULL, maxDurationMs INT DEFAULT NULL, lastRunAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, lastRunStatus VARCHAR(20) DEFAULT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, workflow_id UUID NOT NULL, PRIMARY KEY (id))',
            'CREATE UNIQUE INDEX UNIQ_848B16142C7C2CBA ON etl_workflow_statistic (workflow_id)',
        ];
    }

    /**
     * @return list<string>
     */
    private function postgresConstraints(): array
    {
        return [
            'ALTER TABLE etl_step ADD CONSTRAINT FK_42863395E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE CASCADE NOT DEFERRABLE',
            'ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289C2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) NOT DEFERRABLE',
            'ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289CE80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE SET NULL NOT DEFERRABLE',
            'ALTER TABLE etl_pipeline ADD CONSTRAINT FK_FB3190EF2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) NOT DEFERRABLE',
            'ALTER TABLE etl_pipeline_history ADD CONSTRAINT FK_16086583E80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE CASCADE NOT DEFERRABLE',
            'ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C36880EDCFB258 FOREIGN KEY (pipeline_history_id) REFERENCES etl_pipeline_history (id) ON DELETE CASCADE NOT DEFERRABLE',
            'ALTER TABLE etl_workflow_execution_statistic ADD CONSTRAINT FK_E3F9B09A2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE NOT DEFERRABLE',
            'ALTER TABLE etl_workflow_statistic ADD CONSTRAINT FK_848B16142C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE NOT DEFERRABLE',
        ];
    }
}
