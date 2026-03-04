<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\ETL\Infrastructure\Step\Loader;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Persister\PipelinePersister;
use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\PipelineFactory;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\AbstractLoaderStep;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;

final class WorkflowLoader extends AbstractLoaderStep
{
    public const CODE = 'etl.loader.workflow';

    protected string $code = self::CODE;

    public function __construct(
        private readonly PipelineFactory $pipelineFactory,
        private readonly PipelinePersister $pipelinePersister,
    ) {
    }

    public function getConfigurationDescription(): array
    {
        return [
            'workflowId' => 'The ID of the workflow to execute for loading data.',
            'configuration' => 'The override configuration',
        ];
    }

    public function load(mixed $data, mixed $destination, array $configuration = []): bool
    {
        $workflowId = $configuration['workflowId'] ?? $this->configuration['workflowId'] ?? null;
        $overrideConfiguration = $configuration['configuration'] ?? $this->configuration['configuration'] ?? [];

        if (!is_string($workflowId)) {
            throw new \InvalidArgumentException('Workflow ID must be a string.');
        }

        if (!is_array($data)) {
            $data = [$data];
        }

        foreach ($data as $input) {
            /** @var Pipeline $pipeline */
            $pipeline = $this->pipelineFactory->createFromWorkflowId($workflowId);
            $pipeline->setInput($input);
            $pipeline->setConfiguration($overrideConfiguration);

            $this->pipelinePersister->create($pipeline);
        }

        return true;
    }
}
