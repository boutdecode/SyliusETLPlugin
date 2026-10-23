<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Dto;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\PlannedTask;
use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Workflow;

/**
 * Mutable form model of a planned task: the bundle entity can only be changed through update().
 */
final class PlannedTaskData
{
    public ?string $id = null;

    public string $name = '';

    public bool $enabled = true;

    public ?Workflow $workflow = null;

    public string $schedule = '';

    /** @var array<string, mixed> */
    public array $configuration = [];

    /** @var array<string, mixed> */
    public array $input = [];

    public static function fromPlannedTask(PlannedTask $plannedTask): self
    {
        $workflow = $plannedTask->getWorkflow();

        $data = new self();
        $data->id = $plannedTask->getId();
        $data->name = $plannedTask->getName();
        $data->enabled = $plannedTask->isEnabled();
        $data->workflow = $workflow instanceof Workflow ? $workflow : null;
        $data->schedule = $plannedTask->getSchedule();
        $data->configuration = $plannedTask->getConfiguration();
        $data->input = $plannedTask->getInput();

        return $data;
    }
}
