<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260623000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add name column to etl_pipeline table';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_pipeline ADD name VARCHAR(255) DEFAULT NULL');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_pipeline ADD name VARCHAR(255) DEFAULT NULL');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_pipeline DROP COLUMN name');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_pipeline DROP COLUMN name');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }
}
