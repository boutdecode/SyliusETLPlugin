<?php

declare(strict_types=1);

namespace Tests\BoutDeCode\SyliusETLPlugin\Unit\Statistics\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\WorkflowExecutionStatistic as CoreWorkflowExecutionStatistic;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Factory\WorkflowExecutionStatisticFactory;
use PHPUnit\Framework\TestCase;

final class WorkflowExecutionStatisticFactoryTest extends TestCase
{
    private WorkflowExecutionStatisticFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new WorkflowExecutionStatisticFactory();
    }

    public function testCreateReturnsWorkflowExecutionStatistic(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $startedAt = new \DateTimeImmutable('2026-01-01 10:00:00');
        $finishedAt = new \DateTimeImmutable('2026-01-01 10:00:02');

        $result = $this->factory->create($workflow, PipelineHistoryStatusEnum::COMPLETED, $startedAt, $finishedAt);

        self::assertInstanceOf(CoreWorkflowExecutionStatistic::class, $result);
    }

    public function testCreateAssignsAllFields(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $startedAt = new \DateTimeImmutable('2026-01-01 10:00:00');
        $finishedAt = new \DateTimeImmutable('2026-01-01 10:00:02');

        $result = $this->factory->create($workflow, PipelineHistoryStatusEnum::COMPLETED, $startedAt, $finishedAt);

        self::assertSame($workflow, $result->getWorkflow());
        self::assertSame(PipelineHistoryStatusEnum::COMPLETED, $result->getStatus());
        self::assertSame($startedAt, $result->getStartedAt());
        self::assertSame($finishedAt, $result->getFinishedAt());
    }

    public function testCreateWithFailedStatus(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $startedAt = new \DateTimeImmutable('2026-01-01 10:00:00');
        $finishedAt = new \DateTimeImmutable('2026-01-01 10:00:05');

        $result = $this->factory->create($workflow, PipelineHistoryStatusEnum::FAILED, $startedAt, $finishedAt);

        self::assertSame(PipelineHistoryStatusEnum::FAILED, $result->getStatus());
    }

    public function testGetDurationMsComputesCorrectly(): void
    {
        $workflow = $this->createMock(Workflow::class);
        $startedAt = new \DateTimeImmutable('2026-01-01 10:00:00.000000');
        $finishedAt = new \DateTimeImmutable('2026-01-01 10:00:02.500000');

        $result = $this->factory->create($workflow, PipelineHistoryStatusEnum::COMPLETED, $startedAt, $finishedAt);

        self::assertSame(2500, $result->getDurationMs());
    }
}
