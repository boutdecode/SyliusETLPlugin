<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260511000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add etl_planned_task table';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE etl_planned_task (id UUID NOT NULL, workflow_id UUID DEFAULT NULL, pipeline_id UUID DEFAULT NULL, enabled BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, schedule VARCHAR(255) NOT NULL, configuration JSON NOT NULL, input JSON NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_39A2289C2C7C2CBA ON etl_planned_task (workflow_id)');
            $this->addSql('CREATE INDEX IDX_39A2289CE80B93 ON etl_planned_task (pipeline_id)');
            $this->addSql('COMMENT ON COLUMN etl_planned_task.id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_planned_task.workflow_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_planned_task.pipeline_id IS \'(DC2Type:uuid)\'');
            $this->addSql('COMMENT ON COLUMN etl_planned_task.createdAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('COMMENT ON COLUMN etl_planned_task.updatedAt IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289C2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289CE80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('CREATE TABLE etl_planned_task (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', workflow_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', pipeline_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', enabled TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, schedule VARCHAR(255) NOT NULL, configuration JSON NOT NULL, input JSON NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE INDEX IDX_39A2289C2C7C2CBA ON etl_planned_task (workflow_id)');
            $this->addSql('CREATE INDEX IDX_39A2289CE80B93 ON etl_planned_task (pipeline_id)');
            $this->addSql('ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289C2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES etl_workflow (id)');
            $this->addSql('ALTER TABLE etl_planned_task ADD CONSTRAINT FK_39A2289CE80B93 FOREIGN KEY (pipeline_id) REFERENCES etl_pipeline (id) ON DELETE SET NULL');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_planned_task DROP CONSTRAINT FK_39A2289C2C7C2CBA');
            $this->addSql('ALTER TABLE etl_planned_task DROP CONSTRAINT FK_39A2289CE80B93');
            $this->addSql('DROP TABLE etl_planned_task');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_planned_task DROP FOREIGN KEY FK_39A2289C2C7C2CBA');
            $this->addSql('ALTER TABLE etl_planned_task DROP FOREIGN KEY FK_39A2289CE80B93');
            $this->addSql('DROP TABLE etl_planned_task');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }
}
