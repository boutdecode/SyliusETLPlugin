<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Data\Persister\WorkflowExecutionStatisticPersister;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Data\Provider\WorkflowExecutionStatisticProvider;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\WorkflowExecutionStatistic as CoreWorkflowExecutionStatistic;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Entity\WorkflowExecutionStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkflowExecutionStatistic>
 */
class WorkflowExecutionStatisticRepository extends ServiceEntityRepository implements WorkflowExecutionStatisticPersister, WorkflowExecutionStatisticProvider
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkflowExecutionStatistic::class);
    }

    public function create(CoreWorkflowExecutionStatistic $workflowExecutionStatistic): CoreWorkflowExecutionStatistic
    {
        $this->getEntityManager()->persist($workflowExecutionStatistic);
        $this->getEntityManager()->flush();

        return $workflowExecutionStatistic;
    }

    public function findByWorkflow(Workflow $workflow, int $limit = 100): iterable
    {
        return $this->findBy(['workflow' => $workflow], ['startedAt' => 'DESC'], $limit);
    }

    public function findByWorkflowBetween(Workflow $workflow, \DateTimeImmutable $from, \DateTimeImmutable $to): iterable
    {
        /** @var list<CoreWorkflowExecutionStatistic> $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.workflow = :workflow')
            ->andWhere('e.startedAt >= :from')
            ->andWhere('e.startedAt <= :to')
            ->setParameter('workflow', $workflow)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('e.startedAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /** @return list<array{day: string, status: string, count: int}> */
    public function countPerDayAndStatus(int $days = 30): array
    {
        $from = (new \DateTimeImmutable("-{$days} days"))->format('Y-m-d');
        $sql = 'SELECT DATE(started_at) AS day, status, COUNT(*) AS count
                FROM etl_workflow_execution_statistic
                WHERE started_at >= :from
                GROUP BY DATE(started_at), status
                ORDER BY day ASC';

        /** @var list<array{day: string, status: string, count: string}> $rows */
        $rows = $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['from' => $from])
            ->fetchAllAssociative();

        return array_map(
            static fn (array $row): array => ['day' => $row['day'], 'status' => $row['status'], 'count' => (int) $row['count']],
            $rows,
        );
    }
}
