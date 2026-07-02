<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Migrations;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260702000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add notification preferences (notify_on_success, notify_on_failure, notification_providers) to etl_workflow';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_workflow ADD notify_on_success BOOLEAN NOT NULL DEFAULT false');
            $this->addSql('ALTER TABLE etl_workflow ADD notify_on_failure BOOLEAN NOT NULL DEFAULT false');
            $this->addSql('ALTER TABLE etl_workflow ADD notification_providers JSON DEFAULT NULL');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_workflow ADD notify_on_success TINYINT(1) NOT NULL DEFAULT 0, ADD notify_on_failure TINYINT(1) NOT NULL DEFAULT 0, ADD notification_providers JSON DEFAULT NULL');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE etl_workflow DROP notify_on_success');
            $this->addSql('ALTER TABLE etl_workflow DROP notify_on_failure');
            $this->addSql('ALTER TABLE etl_workflow DROP notification_providers');

            return;
        }

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform) {
            $this->addSql('ALTER TABLE etl_workflow DROP notify_on_success, DROP notify_on_failure, DROP notification_providers');

            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform::class));
    }
}
