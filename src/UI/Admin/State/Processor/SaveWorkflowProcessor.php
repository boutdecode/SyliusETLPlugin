<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository;
use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\WorkflowFactory;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\WorkflowData;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final readonly class SaveWorkflowProcessor implements ProcessorInterface
{
    public function __construct(
        private WorkflowRepository $workflowRepository,
        private WorkflowFactory $workflowFactory,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, WorkflowData::class);

        if (null === $data->id) {
            $workflow = $this->workflowFactory->create(
                $data->name,
                $data->configuration,
                $data->stepConfiguration,
                $data->description,
                $data->notifyOnSuccess,
                $data->notifyOnFailure,
                $data->notificationProviders,
            );

            return $this->workflowRepository->create($workflow);
        }

        $workflow = $this->workflowRepository->find($data->id)
            ?? throw new NotFoundHttpException('Workflow has not been found.');

        $workflow->update(
            $data->name,
            $data->configuration,
            $data->stepConfiguration,
            $data->description,
            $data->notifyOnSuccess,
            $data->notifyOnFailure,
            $data->notificationProviders,
        );

        return $this->workflowRepository->save($workflow);
    }
}
