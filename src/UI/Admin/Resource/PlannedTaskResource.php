<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Resource;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\PlannedTask;

/**
 * Sylius resource marker of the {@see PlannedTask} entity: Sylius only registers classes implementing
 * ResourceInterface, which the entities of etl-bundle do not. It is never instantiated or persisted.
 */
final class PlannedTaskResource implements EtlResourceInterface
{
    public static function getEntityClass(): string
    {
        return PlannedTask::class;
    }

    public function getId(): string
    {
        throw new \LogicException('This class only identifies the Sylius resource.');
    }
}
