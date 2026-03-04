<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Persister\PipelinePersister;
use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Provider\PipelineProvider;
use BoutDeCode\ETLCoreBundle\Core\Domain\Enum\PipelineStatus;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline as CorePipeline;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends ServiceEntityRepository<Pipeline>
 *
 * @method Pipeline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pipeline|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Pipeline[]    findAll()
 * @method Pipeline[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class PipelineRepository extends ServiceEntityRepository implements PipelinePersister, PipelineProvider, RepositoryInterface
{
    use ResourceRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pipeline::class);
    }

    public function create(CorePipeline $pipeline): CorePipeline
    {
        return $this->save($pipeline);
    }

    public function save(CorePipeline $pipeline): CorePipeline
    {
        $this->getEntityManager()->persist($pipeline);
        $this->getEntityManager()->flush();

        return $pipeline;
    }

    public function findPipelineByIdentifier(string $identifier): ?CorePipeline
    {
        return $this->find($identifier);
    }

    public function findScheduledPipelines(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.scheduledAt <= :now OR p.scheduledAt IS NULL')
            ->andWhere('p.status = :status')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', PipelineStatus::PENDING->value)
            ->getQuery()
            ->getResult();
    }
}
