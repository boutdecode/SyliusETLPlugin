<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Factory;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PlannedTaskData;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @implements FactoryInterface<PlannedTaskData>
 */
final readonly class PlannedTaskDataFactory implements FactoryInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private WorkflowRepository $workflowRepository,
    ) {
    }

    public function createNew(): PlannedTaskData
    {
        $data = new PlannedTaskData();

        $workflowId = $this->requestStack->getCurrentRequest()?->query->get('workflowId');
        if (\is_string($workflowId) && '' !== $workflowId) {
            $data->workflow = $this->workflowRepository->find($workflowId);
        }

        return $data;
    }
}
