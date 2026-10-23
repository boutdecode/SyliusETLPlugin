<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\PlannedTask;
use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\PlannedTaskRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PlannedTaskData;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final readonly class SavePlannedTaskProcessor implements ProcessorInterface
{
    public function __construct(
        private PlannedTaskRepository $plannedTaskRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, PlannedTaskData::class);
        Assert::notNull($data->workflow);

        if (null === $data->id) {
            return $this->plannedTaskRepository->create(new PlannedTask(
                $data->name,
                $data->workflow,
                $data->schedule,
                $data->enabled,
                $data->input,
                $data->configuration,
            ));
        }

        $plannedTask = $this->plannedTaskRepository->find($data->id)
            ?? throw new NotFoundHttpException('Planned task has not been found.');

        $plannedTask->update(
            $data->name,
            $data->workflow,
            $data->schedule,
            $data->enabled,
            $data->input,
            $data->configuration,
        );

        return $this->plannedTaskRepository->save($plannedTask);
    }
}
