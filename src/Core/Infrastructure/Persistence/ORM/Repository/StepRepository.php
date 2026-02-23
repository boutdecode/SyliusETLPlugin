<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository;

use Akawaka\ETLCoreBundle\Core\Domain\Data\Persister\StepPersister;
use Akawaka\ETLCoreBundle\Core\Domain\Model\Step as CoreStep;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Step;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Step>
 *
 * @method Step|null find($id, $lockMode = null, $lockVersion = null)
 * @method Step|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Step[]    findAll()
 * @method Step[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class StepRepository extends ServiceEntityRepository implements StepPersister
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Step::class);
    }

    public function create(CoreStep $step): CoreStep
    {
        $this->getEntityManager()->persist($step);
        $this->getEntityManager()->flush();

        return $step;
    }
}
