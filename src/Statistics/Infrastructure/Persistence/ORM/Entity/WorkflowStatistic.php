<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\AbstractWorkflowStatistic;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowStatisticRepository::class)]
#[ORM\Table(name: 'etl_workflow_statistic')]
class WorkflowStatistic extends AbstractWorkflowStatistic
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    private string $id;

    #[ORM\OneToOne(targetEntity: Workflow::class)]
    #[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected CoreWorkflow $workflow;

    #[ORM\Column(type: 'integer', name: 'total_count')]
    protected int $totalCount = 0;

    #[ORM\Column(type: 'integer', name: 'success_count')]
    protected int $successCount = 0;

    #[ORM\Column(type: 'integer', name: 'failure_count')]
    protected int $failureCount = 0;

    #[ORM\Column(type: 'integer', name: 'total_duration_ms')]
    protected int $totalDurationMs = 0;

    #[ORM\Column(type: 'integer', nullable: true, name: 'min_duration_ms')]
    protected ?int $minDurationMs = null;

    #[ORM\Column(type: 'integer', nullable: true, name: 'max_duration_ms')]
    protected ?int $maxDurationMs = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, name: 'last_run_at')]
    protected ?\DateTimeImmutable $lastRunAt = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: PipelineHistoryStatusEnum::class, name: 'last_run_status')]
    protected ?PipelineHistoryStatusEnum $lastRunStatus = null;

    #[ORM\Column(type: 'datetime_immutable', name: 'updated_at')]
    protected \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setWorkflow(CoreWorkflow $workflow): void
    {
        $this->workflow = $workflow;
    }
}
