<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Factory\WorkflowExecutionStatisticFactory as CoreWorkflowExecutionStatisticFactory;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\WorkflowExecutionStatistic as CoreWorkflowExecutionStatistic;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Entity\WorkflowExecutionStatistic;

final class WorkflowExecutionStatisticFactory implements CoreWorkflowExecutionStatisticFactory
{
    public function create(
        Workflow $workflow,
        PipelineHistoryStatusEnum $status,
        \DateTimeImmutable $startedAt,
        \DateTimeImmutable $finishedAt,
    ): CoreWorkflowExecutionStatistic {
        $statistic = new WorkflowExecutionStatistic();
        $statistic->setWorkflow($workflow);
        $statistic->setStatus($status);
        $statistic->setStartedAt($startedAt);
        $statistic->setFinishedAt($finishedAt);

        return $statistic;
    }
}
