<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\PlannedTaskRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PlannedTaskData;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Context\Option\RequestOption;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class PlannedTaskDataProvider implements ProviderInterface
{
    public function __construct(
        private PlannedTaskRepository $plannedTaskRepository,
    ) {
    }

    public function provide(Operation $operation, Context $context): PlannedTaskData
    {
        $id = $context->get(RequestOption::class)?->request()->attributes->get('id');
        $plannedTask = \is_string($id) ? $this->plannedTaskRepository->find($id) : null;
        if (null === $plannedTask) {
            throw new NotFoundHttpException('Planned task has not been found.');
        }

        return PlannedTaskData::fromPlannedTask($plannedTask);
    }
}
