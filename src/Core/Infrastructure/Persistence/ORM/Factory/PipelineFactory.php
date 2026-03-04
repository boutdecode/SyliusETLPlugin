<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Provider\WorkflowProvider;
use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\PipelineFactory as CorePipelineFactory;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline as CorePipeline;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Step;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use Webmozart\Assert\Assert;

class PipelineFactory implements CorePipelineFactory
{
    public function __construct(
        private readonly WorkflowProvider $workflowProvider,
        private readonly StepFactory $stepFactory,
    ) {
    }

    /** @param Step[] $steps */
    public function create(
        array $steps = [],
        array $configuration = [],
        array $input = [],
    ): CorePipeline
    {
        $pipeline = new Pipeline();
        $pipeline->setSteps($steps);
        $pipeline->setConfiguration($configuration);
        $pipeline->setInput($input);

        return $pipeline;
    }

    public function createFromWorkflowId(
        string $workflowId,
        array $overrideConfiguration = [],
        array $input = [],
    ): CorePipeline
    {
        $workflow = $this->workflowProvider->findWorkflowByIdentifier($workflowId);

        Assert::isInstanceOf($workflow, Workflow::class);

        $steps = [];
        foreach ($workflow->getStepConfiguration() as $index => $stepConfiguration) {
            $steps[] = $this->stepFactory->create(
                code: $stepConfiguration['code'],
                name: $stepConfiguration['name'] ?? null,
                configuration: $stepConfiguration['configuration'] ?? [],
                order: $index,
            );
        }

        $configuration = array_merge($workflow->getConfiguration(), $overrideConfiguration);

        $step = $this->create($steps, $configuration, $input);
        $step->setWorkflow($workflow);

        return $step;
    }
}
