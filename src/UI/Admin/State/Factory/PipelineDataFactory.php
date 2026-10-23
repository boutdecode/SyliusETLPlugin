<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Factory;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PipelineData;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @implements FactoryInterface<PipelineData>
 */
final readonly class PipelineDataFactory implements FactoryInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private WorkflowRepository $workflowRepository,
    ) {
    }

    public function createNew(): PipelineData
    {
        $data = new PipelineData();
        $data->scheduledAt = new \DateTimeImmutable('+1 minute');

        $workflowId = $this->requestStack->getCurrentRequest()?->query->get('workflowId');
        if (\is_string($workflowId) && '' !== $workflowId) {
            $data->workflow = $this->workflowRepository->find($workflowId);
            $data->configuration = $data->workflow?->getStepConfiguration() ?? [];
        }

        return $data;
    }
}
