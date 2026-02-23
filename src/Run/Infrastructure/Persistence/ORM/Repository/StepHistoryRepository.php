<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository;

use Akawaka\ETLCoreBundle\Run\Domain\Data\Persister\StepHistoryPersister;
use Akawaka\ETLCoreBundle\Run\Domain\Model\StepHistory as CoreStepHistory;
use Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\StepHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StepHistory>
 *
 * @method StepHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StepHistory|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method StepHistory[]    findAll()
 * @method StepHistory[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class StepHistoryRepository extends ServiceEntityRepository implements StepHistoryPersister
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StepHistory::class);
    }

    public function create(CoreStepHistory $stepHistory): CoreStepHistory
    {
        $this->getEntityManager()->persist($stepHistory);
        $this->getEntityManager()->flush();

        return $stepHistory;
    }
}
