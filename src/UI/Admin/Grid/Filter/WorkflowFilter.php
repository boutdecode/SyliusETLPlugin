<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\Filter;

use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Sylius\Bundle\GridBundle\Form\Type\Filter\EntityFilterType;
use Sylius\Component\Grid\Attribute\AsFilter;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Filters a grid by a workflow relation, binding the identifier with the DBAL "uuid" type
 * so that it works with binary(16) columns as well as native/text UUID columns.
 */
#[AsFilter(formType: EntityFilterType::class, type: self::TYPE, template: '@SyliusUi/Grid/Filter/entity.html.twig')]
final class WorkflowFilter implements FilterInterface
{
    public const TYPE = 'etl_workflow';

    private int $counter = 0;

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        if (!$dataSource instanceof DataSource) {
            throw new \InvalidArgumentException(sprintf('"%s" only supports "%s".', self::class, DataSource::class));
        }

        $identifier = $data instanceof Workflow ? $data->getId() : (\is_scalar($data) || $data instanceof \Stringable ? (string) $data : '');
        if (!Uuid::isValid($identifier)) {
            // Unknown identifier: match nothing rather than failing on the binary conversion.
            $dataSource->restrict($dataSource->getQueryBuilder()->expr()->eq('1', '0'));

            return;
        }

        $queryBuilder = $dataSource->getQueryBuilder();
        $alias = $queryBuilder->getRootAliases()[0];
        $parameter = sprintf('%s_filter_%d', preg_replace('/\W/', '_', $name), $this->counter++);

        $queryBuilder->setParameter($parameter, Uuid::fromString($identifier), 'uuid');
        $dataSource->restrict($queryBuilder->expr()->eq(sprintf('%s.%s', $alias, $name), ':' . $parameter));
    }
}
