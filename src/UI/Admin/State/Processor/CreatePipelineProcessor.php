<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor;

use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\PipelineFactory;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Doctrine\Common\State\PersistProcessor;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Webmozart\Assert\Assert;

final class CreatePipelineProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly PersistProcessor $decorated,
        private readonly PipelineFactory $pipelineFactory,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, Pipeline::class);

        $workflow = $data->getWorkflow();
        Assert::isInstanceOf($workflow, Workflow::class);

        $pipeline = $this->pipelineFactory->createFromWorkflowId(
            workflowId: $workflow->getId(),
            overrideConfiguration: $data->getConfiguration(),
            input: $data->getInput(),
        );

        return $this->decorated->process($pipeline, $operation, $context);
    }
}
