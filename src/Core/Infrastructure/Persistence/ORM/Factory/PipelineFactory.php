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

    /**
     * @param Step[] $steps
     * @param array<string, mixed> $configuration
     * @param array<string, mixed> $input
     */
    public function create(
        array $steps = [],
        array $configuration = [],
        array $input = [],
    ): CorePipeline {
        $pipeline = new Pipeline();
        $pipeline->setSteps($steps);
        $pipeline->setConfiguration($configuration);
        $pipeline->setInput($input);

        return $pipeline;
    }

    /**
     * @param array<string, mixed> $overrideConfiguration
     * @param array<string, mixed> $input
     */
    public function createFromWorkflowId(
        string $workflowId,
        array $overrideConfiguration = [],
        array $input = [],
    ): CorePipeline {
        $workflow = $this->workflowProvider->findWorkflowByIdentifier($workflowId);

        Assert::isInstanceOf($workflow, Workflow::class);

        $steps = [];
        foreach ($workflow->getStepConfiguration() as $index => $stepConfiguration) {
            Assert::isArray($stepConfiguration);
            Assert::keyExists($stepConfiguration, 'code');
            Assert::string($stepConfiguration['code']);

            $code = $stepConfiguration['code'];
            $name = isset($stepConfiguration['name']) && is_string($stepConfiguration['name']) ? $stepConfiguration['name'] : null;
            /** @var array<string, mixed> $stepConfig */
            $stepConfig = isset($stepConfiguration['configuration']) && is_array($stepConfiguration['configuration']) ? $stepConfiguration['configuration'] : [];
            $steps[] = $this->stepFactory->create(
                code: $code,
                name: $name,
                configuration: $stepConfig,
                order: (int) $index,
            );
        }

        $configuration = array_merge($workflow->getConfiguration(), $overrideConfiguration);

        $pipeline = $this->create($steps, $configuration, $input);
        Assert::isInstanceOf($pipeline, Pipeline::class);

        $pipeline->setWorkflow($workflow);

        return $pipeline;
    }
}
