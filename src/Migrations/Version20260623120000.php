<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260623120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ON DELETE CASCADE to etl_step_history.pipeline_history_id FK';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_step_history DROP CONSTRAINT FK_47C368806F83648D');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C368806F83648D FOREIGN KEY (pipeline_history_id) REFERENCES etl_pipeline_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_step_history DROP FOREIGN KEY FK_47C368806F83648D');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C368806F83648D FOREIGN KEY (pipeline_history_id) REFERENCES etl_pipeline_history (id) ON DELETE CASCADE');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_step_history DROP CONSTRAINT FK_47C368806F83648D');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C368806F83648D FOREIGN KEY (pipeline_history_id) REFERENCES etl_pipeline_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_step_history DROP FOREIGN KEY FK_47C368806F83648D');
            $this->addSql('ALTER TABLE etl_step_history ADD CONSTRAINT FK_47C368806F83648D FOREIGN KEY (pipeline_history_id) REFERENCES etl_pipeline_history (id)');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }
}
