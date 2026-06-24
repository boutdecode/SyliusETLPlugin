<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create etl_workflow_statistic and etl_workflow_execution_statistic tables';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE etl_workflow_statistic (
                id UUID NOT NULL,
                workflow_id UUID NOT NULL,
                total_count INT NOT NULL DEFAULT 0,
                success_count INT NOT NULL DEFAULT 0,
                failure_count INT NOT NULL DEFAULT 0,
                total_duration_ms INT NOT NULL DEFAULT 0,
                min_duration_ms INT DEFAULT NULL,
                max_duration_ms INT DEFAULT NULL,
                last_run_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                last_run_status VARCHAR(20) DEFAULT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('COMMENT ON COLUMN etl_workflow_statistic.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow_statistic.workflow_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow_statistic.last_run_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow_statistic.updated_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_workflow_statistic_workflow ON etl_workflow_statistic (workflow_id)');
            $this->addSql('ALTER TABLE etl_workflow_statistic ADD CONSTRAINT FK_etl_workflow_statistic_workflow FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

            $this->addSql('CREATE TABLE etl_workflow_execution_statistic (
                id UUID NOT NULL,
                workflow_id UUID NOT NULL,
                status VARCHAR(20) NOT NULL,
                started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                finished_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )');
            $this->addSql('COMMENT ON COLUMN etl_workflow_execution_statistic.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow_execution_statistic.workflow_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow_execution_statistic.started_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_workflow_execution_statistic.finished_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE INDEX IDX_workflow_execution_statistic_workflow ON etl_workflow_execution_statistic (workflow_id)');
            $this->addSql('CREATE INDEX IDX_workflow_execution_statistic_started_at ON etl_workflow_execution_statistic (started_at)');
            $this->addSql('ALTER TABLE etl_workflow_execution_statistic ADD CONSTRAINT FK_etl_workflow_execution_statistic_workflow FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('CREATE TABLE etl_workflow_statistic (
                id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
                workflow_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
                total_count INT NOT NULL DEFAULT 0,
                success_count INT NOT NULL DEFAULT 0,
                failure_count INT NOT NULL DEFAULT 0,
                total_duration_ms INT NOT NULL DEFAULT 0,
                min_duration_ms INT DEFAULT NULL,
                max_duration_ms INT DEFAULT NULL,
                last_run_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                last_run_status VARCHAR(20) DEFAULT NULL,
                updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                PRIMARY KEY(id),
                UNIQUE INDEX UNIQ_workflow_statistic_workflow (workflow_id),
                CONSTRAINT FK_etl_workflow_statistic_workflow FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

            $this->addSql('CREATE TABLE etl_workflow_execution_statistic (
                id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
                workflow_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
                status VARCHAR(20) NOT NULL,
                started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                finished_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                PRIMARY KEY(id),
                INDEX IDX_workflow_execution_statistic_workflow (workflow_id),
                INDEX IDX_workflow_execution_statistic_started_at (started_at),
                CONSTRAINT FK_etl_workflow_execution_statistic_workflow FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE etl_workflow_execution_statistic');
        $this->addSql('DROP TABLE etl_workflow_statistic');
    }
}
