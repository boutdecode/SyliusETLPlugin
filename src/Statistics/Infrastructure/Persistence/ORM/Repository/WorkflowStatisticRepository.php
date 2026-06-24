<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Data\Persister\WorkflowStatisticPersister;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Data\Provider\WorkflowStatisticProvider;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\WorkflowStatistic as CoreWorkflowStatistic;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Entity\WorkflowStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkflowStatistic>
 */
class WorkflowStatisticRepository extends ServiceEntityRepository implements WorkflowStatisticPersister, WorkflowStatisticProvider
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkflowStatistic::class);
    }

    public function create(CoreWorkflowStatistic $workflowStatistic): CoreWorkflowStatistic
    {
        $this->getEntityManager()->persist($workflowStatistic);
        $this->getEntityManager()->flush();

        return $workflowStatistic;
    }

    public function save(CoreWorkflowStatistic $workflowStatistic): CoreWorkflowStatistic
    {
        $this->getEntityManager()->flush();

        return $workflowStatistic;
    }

    public function findByWorkflow(Workflow $workflow): ?CoreWorkflowStatistic
    {
        return $this->findOneBy(['workflow' => $workflow]);
    }

    public function findAll(): iterable
    {
        return parent::findAll();
    }
}
