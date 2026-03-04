<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Run\Domain\Data\Persister\PipelineHistoryPersister;
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
class PipelineHistoryRepository extends ServiceEntityRepository implements PipelineHistoryPersister
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
}
