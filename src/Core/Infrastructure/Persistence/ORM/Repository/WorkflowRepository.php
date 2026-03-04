<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Persister\WorkflowPersister;
use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Provider\WorkflowProvider;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends ServiceEntityRepository<Workflow>
 *
 * @method Workflow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workflow|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Workflow[]    findAll()
 * @method Workflow[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class WorkflowRepository extends ServiceEntityRepository implements WorkflowPersister, WorkflowProvider, RepositoryInterface
{
    use ResourceRepositoryTrait;

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
