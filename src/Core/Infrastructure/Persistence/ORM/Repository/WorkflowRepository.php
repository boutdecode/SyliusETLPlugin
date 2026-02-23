<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository;

use Akawaka\ETLCoreBundle\Core\Domain\Data\Persister\WorkflowPersister;
use Akawaka\ETLCoreBundle\Core\Domain\Data\Provider\WorkflowProvider;
use Akawaka\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Workflow>
 *
 * @method Workflow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workflow|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Workflow[]    findAll()
 * @method Workflow[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class WorkflowRepository extends ServiceEntityRepository implements WorkflowPersister, WorkflowProvider
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workflow::class);
    }

    public function create(CoreWorkflow $workflow): CoreWorkflow
    {
        $this->getEntityManager()->persist($workflow);
        $this->getEntityManager()->flush();

        return $workflow;
    }

    public function findWorkflowByIdentifier(string $identifier): ?CoreWorkflow
    {
        return $this->find($identifier);
    }
}
