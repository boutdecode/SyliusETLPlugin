<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Resource;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;

/**
 * Sylius resource marker of the {@see Pipeline} entity: Sylius only registers classes implementing
 * ResourceInterface, which the entities of etl-bundle do not. It is never instantiated or persisted.
 */
final class PipelineResource implements EtlResourceInterface
{
    public static function getEntityClass(): string
    {
        return Pipeline::class;
    }

    public function getId(): string
    {
        throw new \LogicException('This class only identifies the Sylius resource.');
    }
}
