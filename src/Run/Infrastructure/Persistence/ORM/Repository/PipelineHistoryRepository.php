<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline;
use BoutDeCode\ETLCoreBundle\Run\Domain\Data\Persister\PipelineHistoryPersister;
use BoutDeCode\ETLCoreBundle\Run\Domain\Data\Provider\PipelineHistoryProvider;
use BoutDeCode\ETLCoreBundle\Run\Domain\Model\PipelineHistory as CorePipelineHistory;
use BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\PipelineHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PipelineHistory>
 *
 * @method PipelineHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PipelineHistory|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method PipelineHistory[]    findAll()
 * @method PipelineHistory[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class PipelineHistoryRepository extends ServiceEntityRepository implements PipelineHistoryPersister, PipelineHistoryProvider
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PipelineHistory::class);
    }

    public function create(CorePipelineHistory $pipelineHistory): CorePipelineHistory
    {
        $this->getEntityManager()->persist($pipelineHistory);
        $this->getEntityManager()->flush();

        return $pipelineHistory;
    }

    public function findByPipeline(Pipeline $pipeline, int $limit = 100): iterable
    {
        return $this->findBy(['pipeline' => $pipeline], ['createdAt' => 'DESC'], $limit);
    }

    public function findByPipelineBetween(Pipeline $pipeline, \DateTimeImmutable $from, \DateTimeImmutable $to): iterable
    {
        /** @var list<\BoutDeCode\ETLCoreBundle\Run\Domain\Model\PipelineHistory> $result */
        $result = $this->createQueryBuilder('h')
            ->where('h.pipeline = :pipeline')
            ->andWhere('h.createdAt >= :from')
            ->andWhere('h.createdAt <= :to')
            ->setParameter('pipeline', $pipeline)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('h.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /** @return list<array{day: string, status: string, count: int}> */
    public function countPerDayAndStatus(int $days = 30): array
    {
        $from = (new \DateTimeImmutable("-{$days} days"))->format('Y-m-d');
        $sql = 'SELECT DATE(created_at) AS day, status, COUNT(*) AS count
                FROM etl_pipeline_history
                WHERE created_at >= :from
                GROUP BY DATE(created_at), status
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
