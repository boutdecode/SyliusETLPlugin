<?php

declare(strict_types=1);

namespace Tests\BoutDeCode\SyliusETLPlugin\Unit\UI\Admin\Grid\Filter;

use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\Filter\WorkflowFilter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Symfony\Component\Uid\Uuid;

final class WorkflowFilterTest extends TestCase
{
    private const UUID = '9db0f002-6f10-11f1-850f-da73bd1b7f66';

    private WorkflowFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new WorkflowFilter();
    }

    public function testEmptyDataDoesNotRestrict(): void
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->expects(self::never())->method('andWhere');

        $this->filter->apply(new DataSource($queryBuilder, false, false), 'workflow', '', []);
        $this->filter->apply(new DataSource($queryBuilder, false, false), 'workflow', null, []);
    }

    public function testUuidStringIsBoundWithUuidType(): void
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->expects(self::once())->method('setParameter')->with(
            'workflow_filter_0',
            self::callback(static fn (mixed $v): bool => $v instanceof Uuid && self::UUID === $v->toRfc4122()),
            'uuid',
        );
        $queryBuilder->expects(self::once())->method('andWhere')->with(
            self::callback(static fn (mixed $e): bool => 'o.workflow = :workflow_filter_0' === (string) $e),
        );

        $this->filter->apply(new DataSource($queryBuilder, false, false), 'workflow', self::UUID, []);
    }

    public function testEntityUsesItsIdentifier(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $workflow->method('getId')->willReturn(self::UUID);

        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->expects(self::once())->method('setParameter')->with(
            'workflow_filter_0',
            self::callback(static fn (mixed $v): bool => $v instanceof Uuid && self::UUID === $v->toRfc4122()),
            'uuid',
        );
        $queryBuilder->expects(self::once())->method('andWhere');

        $this->filter->apply(new DataSource($queryBuilder, false, false), 'workflow', $workflow, []);
    }

    public function testParameterNamesAreUniqueAcrossCalls(): void
    {
        $queryBuilder = $this->createQueryBuilder();
        $names = [];
        $queryBuilder->method('setParameter')->willReturnCallback(
            static function (string $name) use (&$names, $queryBuilder): QueryBuilder {
                $names[] = $name;

                return $queryBuilder;
            },
        );

        $this->filter->apply(new DataSource($queryBuilder, false, false), 'workflow', self::UUID, []);
        $this->filter->apply(new DataSource($queryBuilder, false, false), 'workflow', self::UUID, []);

        self::assertCount(2, array_unique($names));
    }

    private function createQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$this->createMock(EntityManagerInterface::class)])
            ->onlyMethods(['getRootAliases', 'setParameter', 'andWhere', 'expr'])
            ->getMock();
        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryBuilder->method('expr')->willReturn(new Expr());
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();

        return $queryBuilder;
    }
}
