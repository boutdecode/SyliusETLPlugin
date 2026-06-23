<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Persister\PlannedTaskPersister;
use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Provider\PlannedTaskProvider;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\PlannedTask as CorePlannedTask;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\PlannedTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

/**
 * @extends ServiceEntityRepository<PlannedTask>
 *
 * @method PlannedTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlannedTask|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method PlannedTask[]    findAll()
 * @method PlannedTask[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class PlannedTaskRepository extends ServiceEntityRepository implements PlannedTaskProvider, PlannedTaskPersister
{
    use ResourceRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlannedTask::class);
    }

    public function create(CorePlannedTask $plannedTask): PlannedTask
    {
        return $this->save($plannedTask);
    }

    public function save(CorePlannedTask $plannedTask): PlannedTask
    {
        $this->getEntityManager()->persist($plannedTask);
        $this->getEntityManager()->flush();

        return $plannedTask;
    }

    public function findByIdentifier(string $identifier): ?PlannedTask
    {
        return $this->find($identifier);
    }

    public function findScheduled(): array
    {
        /** @var array<PlannedTask> $result */
        $result = $this->createQueryBuilder('ps')
            ->andWhere('ps.enabled = true')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
