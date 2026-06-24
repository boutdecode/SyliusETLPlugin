<?php

declare(strict_types=1);

namespace Tests\BoutDeCode\SyliusETLPlugin\Unit\Statistics\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\WorkflowStatistic as CoreWorkflowStatistic;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Factory\WorkflowStatisticFactory;
use PHPUnit\Framework\TestCase;

final class WorkflowStatisticFactoryTest extends TestCase
{
    private WorkflowStatisticFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new WorkflowStatisticFactory();
    }

    public function testCreateReturnsWorkflowStatistic(): void
    {
        $workflow = $this->createMock(Workflow::class);

        $result = $this->factory->create($workflow);

        self::assertInstanceOf(CoreWorkflowStatistic::class, $result);
    }

    public function testCreateAssignsWorkflow(): void
    {
        $workflow = $this->createMock(Workflow::class);

        $result = $this->factory->create($workflow);

        self::assertSame($workflow, $result->getWorkflow());
    }

    public function testCreateInitializesCountersToZero(): void
    {
        $workflow = $this->createMock(Workflow::class);

        $result = $this->factory->create($workflow);

        self::assertSame(0, $result->getTotalCount());
        self::assertSame(0, $result->getSuccessCount());
        self::assertSame(0, $result->getFailureCount());
    }

    public function testCreateInitializesDurationToZero(): void
    {
        $workflow = $this->createMock(Workflow::class);

        $result = $this->factory->create($workflow);

        self::assertSame(0, $result->getTotalDurationMs());
        self::assertNull($result->getMinDurationMs());
        self::assertNull($result->getMaxDurationMs());
        self::assertNull($result->getAverageDurationMs());
    }

    public function testSuccessRateIsZeroOnNewStatistic(): void
    {
        $workflow = $this->createMock(Workflow::class);

        $result = $this->factory->create($workflow);

        self::assertSame(0.0, $result->getSuccessRate());
    }
}
