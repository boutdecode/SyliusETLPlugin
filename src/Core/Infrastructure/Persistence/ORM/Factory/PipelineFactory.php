<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Provider\WorkflowProvider;
use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\PipelineFactory as CorePipelineFactory;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline as CorePipeline;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Step;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow as WorkflowEntity;
use Webmozart\Assert\Assert;

class PipelineFactory implements CorePipelineFactory
{
    public function __construct(
        private readonly WorkflowProvider $workflowProvider,
        private readonly StepFactory $stepFactory,
    ) {
    }

    /**
     * @param Step[] $steps
     * @param array<string, mixed> $configuration
     */
    public function create(
        ?string $name = null,
        array $steps = [],
        array $configuration = [],
    ): CorePipeline {
        $pipeline = new Pipeline();
        $pipeline->setName($name);
        $pipeline->setSteps($steps);
        $pipeline->setConfiguration($configuration);

        return $pipeline;
    }

    /** @deprecated Use createFromWorkflow instead */
    public function createFromWorkflowId(
        string $workflowId,
        array $overrideConfiguration = [],
        array $input = [],
    ): CorePipeline {
        $workflow = $this->workflowProvider->findWorkflowByIdentifier($workflowId);
        Assert::isInstanceOf($workflow, Workflow::class);

        return $this->createFromWorkflow($workflow, null, $overrideConfiguration, $input);
    }

    /**
     * @param array<string, mixed> $overrideConfiguration
     * @param array<string, mixed> $input
     */
    public function createFromWorkflow(
        Workflow $workflow,
        ?string $name = null,
        array $overrideConfiguration = [],
        array $input = [],
    ): CorePipeline {
        Assert::isInstanceOf($workflow, WorkflowEntity::class);

        $steps = [];
        foreach ($workflow->getStepConfiguration() as $index => $stepConfiguration) {
            Assert::isArray($stepConfiguration);
            Assert::keyExists($stepConfiguration, 'code');
            Assert::string($stepConfiguration['code']);

            $code = $stepConfiguration['code'];
            $stepName = isset($stepConfiguration['name']) && is_string($stepConfiguration['name']) ? $stepConfiguration['name'] : null;
            /** @var array<string, mixed> $stepConfig */
            $stepConfig = isset($stepConfiguration['configuration']) && is_array($stepConfiguration['configuration']) ? $stepConfiguration['configuration'] : [];
            $steps[] = $this->stepFactory->create(
                code: $code,
                name: $stepName,
                configuration: $stepConfig,
                order: (int) $index,
            );
        }

        $pipeline = $this->create($name, $steps, array_merge($workflow->getConfiguration(), $overrideConfiguration));
        Assert::isInstanceOf($pipeline, Pipeline::class);

        $pipeline->setInput($input);
        $pipeline->setWorkflow($workflow);

        return $pipeline;
    }
}
