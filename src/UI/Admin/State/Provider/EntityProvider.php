<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider;

use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\EtlResourceInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Context\Option\RequestOption;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProviderInterface;
use Webmozart\Assert\Assert;

/**
 * Loads the etl-bundle entity behind an {@see EtlResourceInterface} marker from the "id" route attribute.
 */
final readonly class EntityProvider implements ProviderInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function provide(Operation $operation, Context $context): ?object
    {
        $resourceClass = $operation->getResource()?->getClass();
        Assert::string($resourceClass);
        Assert::isAOf($resourceClass, EtlResourceInterface::class);

        $id = $context->get(RequestOption::class)?->request()->attributes->get('id');
        if (!\is_string($id)) {
            return null;
        }

        return $this->managerRegistry->getRepository($resourceClass::getEntityClass())->find($id);
    }
}
