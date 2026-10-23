<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Persister\PipelinePersister;
use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\PipelineFactory;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PipelineData;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Webmozart\Assert\Assert;

final readonly class CreatePipelineProcessor implements ProcessorInterface
{
    public function __construct(
        private PipelinePersister $pipelinePersister,
        private PipelineFactory $pipelineFactory,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, PipelineData::class);
        Assert::notNull($data->workflow);

        $pipeline = $this->pipelineFactory->createFromWorkflow(
            workflow: $data->workflow,
            name: $data->name,
            overrideConfiguration: $data->configuration,
            input: $data->input,
        );

        if (null !== $data->scheduledAt) {
            $pipeline->plan($data->scheduledAt);
        }

        return $this->pipelinePersister->create($pipeline);
    }
}
