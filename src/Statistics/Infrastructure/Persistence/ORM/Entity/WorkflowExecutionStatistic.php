<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Statistics\Domain\Model\AbstractWorkflowExecutionStatistic;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowExecutionStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowExecutionStatisticRepository::class)]
#[ORM\Table(name: 'etl_workflow_execution_statistic')]
class WorkflowExecutionStatistic extends AbstractWorkflowExecutionStatistic
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Workflow::class)]
    #[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected CoreWorkflow $workflow;

    #[ORM\Column(type: 'string', enumType: PipelineHistoryStatusEnum::class, name: 'status')]
    protected PipelineHistoryStatusEnum $status;

    #[ORM\Column(type: 'datetime_immutable', name: 'started_at')]
    protected \DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'datetime_immutable', name: 'finished_at')]
    protected \DateTimeImmutable $finishedAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setWorkflow(CoreWorkflow $workflow): void
    {
        $this->workflow = $workflow;
    }

    public function setStatus(PipelineHistoryStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }
}
