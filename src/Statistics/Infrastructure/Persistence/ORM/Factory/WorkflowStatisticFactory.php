<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Factory\WorkflowStatisticFactory as CoreWorkflowStatisticFactory;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\WorkflowStatistic as CoreWorkflowStatistic;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Entity\WorkflowStatistic;

final class WorkflowStatisticFactory implements CoreWorkflowStatisticFactory
{
    public function create(Workflow $workflow): CoreWorkflowStatistic
    {
        $statistic = new WorkflowStatistic();
        $statistic->setWorkflow($workflow);

        return $statistic;
    }
}
