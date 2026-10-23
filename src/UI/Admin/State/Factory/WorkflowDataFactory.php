<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Factory;

use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\WorkflowData;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @implements FactoryInterface<WorkflowData>
 */
final class WorkflowDataFactory implements FactoryInterface
{
    public function createNew(): WorkflowData
    {
        return new WorkflowData();
    }
}
