<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Dto;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Workflow;

/**
 * Form model of a pipeline to launch from a workflow.
 */
final class PipelineData
{
    public ?string $name = null;

    public ?Workflow $workflow = null;

    /** @var array<string, mixed> */
    public array $configuration = [];

    /** @var array<string, mixed> */
    public array $input = [];

    public ?\DateTimeImmutable $scheduledAt = null;
}
