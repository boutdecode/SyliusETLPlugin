<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity;

use Akawaka\ETLCoreBundle\Run\Domain\Enum\StepHistoryStatusEnum;
use Akawaka\ETLCoreBundle\Run\Domain\Model\AbstractStepHistory;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Step;
use Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository\StepHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StepHistoryRepository::class)]
#[ORM\Table(name: 'etl_step_history')]
class StepHistory extends AbstractStepHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    protected string $id;

    #[ORM\Column(type: 'string', enumType: StepHistoryStatusEnum::class)]
    protected StepHistoryStatusEnum $status;

    #[ORM\Column(type: 'json', nullable: true)]
    protected mixed $input = [];

    #[ORM\Column(type: 'json', nullable: true)]
    protected mixed $result = [];

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: PipelineHistory::class, inversedBy: 'stepHistories')]
    protected PipelineHistory $pipelineHistory;

    #[ORM\ManyToOne(targetEntity: Step::class)]
    #[ORM\JoinColumn(name: 'step_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected Step $step;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getPipelineHistory(): PipelineHistory
    {
        return $this->pipelineHistory;
    }

    public function setPipelineHistory(PipelineHistory $pipelineHistory): void
    {
        $this->pipelineHistory = $pipelineHistory;
    }

    public function setStatus(StepHistoryStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function setInput(mixed $input): void
    {
        $this->input = $input;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }

    public function getStep(): Step
    {
        return $this->step;
    }

    public function setStep(Step $step): void
    {
        $this->step = $step;
    }
}
