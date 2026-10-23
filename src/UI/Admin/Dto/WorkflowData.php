<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Dto;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Workflow;

/**
 * Mutable form model of a workflow: the bundle entity can only be changed through update().
 */
final class WorkflowData
{
    public ?string $id = null;

    public string $name = '';

    public ?string $description = null;

    /** @var array<string, mixed> */
    public array $configuration = [];

    /** @var array<string, mixed> */
    public array $stepConfiguration = [];

    public bool $notifyOnSuccess = false;

    public bool $notifyOnFailure = false;

    /** @var string[]|null */
    public ?array $notificationProviders = null;

    public static function fromWorkflow(Workflow $workflow): self
    {
        $data = new self();
        $data->id = $workflow->getId();
        $data->name = $workflow->getName();
        $data->description = $workflow->getDescription();
        $data->configuration = $workflow->getConfiguration();
        $data->stepConfiguration = $workflow->getStepConfiguration();
        $data->notifyOnSuccess = $workflow->isNotifyOnSuccess();
        $data->notifyOnFailure = $workflow->isNotifyOnFailure();
        $data->notificationProviders = $workflow->getNotificationProviders();

        return $data;
    }
}
