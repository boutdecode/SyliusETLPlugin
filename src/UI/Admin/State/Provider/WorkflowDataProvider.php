<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\WorkflowData;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Context\Option\RequestOption;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class WorkflowDataProvider implements ProviderInterface
{
    public function __construct(
        private WorkflowRepository $workflowRepository,
    ) {
    }

    public function provide(Operation $operation, Context $context): WorkflowData
    {
        $id = $context->get(RequestOption::class)?->request()->attributes->get('id');
        $workflow = \is_string($id) ? $this->workflowRepository->find($id) : null;
        if (null === $workflow) {
            throw new NotFoundHttpException('Workflow has not been found.');
        }

        return WorkflowData::fromWorkflow($workflow);
    }
}
