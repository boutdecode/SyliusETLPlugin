<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\UI\Admin\State\Processor;

use Akawaka\ETLCoreBundle\Core\Domain\Factory\PipelineFactory;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
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

        $pipeline = $this->pipelineFactory->createFromWorkflowId(
            workflowId: $data->getWorkflow()->getId(),
            overrideConfiguration: $data->getConfiguration(),
            input: $data->getInput(),
        );

        return $this->decorated->process($pipeline, $operation, $context);
    }
}
