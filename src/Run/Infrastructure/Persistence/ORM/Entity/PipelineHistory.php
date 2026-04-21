<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Run\Domain\Model\AbstractPipelineHistory;
use BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository\PipelineHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PipelineHistoryRepository::class)]
#[ORM\Table(name: 'etl_pipeline_history')]
class PipelineHistory extends AbstractPipelineHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    protected string $id;

    #[ORM\Column(type: 'string', enumType: PipelineHistoryStatusEnum::class)]
    protected PipelineHistoryStatusEnum $status;

    #[ORM\Column(type: 'json', nullable: true)]
    protected mixed $input = [];

    #[ORM\Column(type: 'json', nullable: true)]
    protected mixed $result = [];

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $createdAt;

    /** @var StepHistory[] */
    #[ORM\OneToMany(targetEntity: StepHistory::class, mappedBy: 'pipelineHistory', cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected iterable $stepHistories = [];

    #[ORM\ManyToOne(targetEntity: Pipeline::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(name: 'pipeline_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setStatus(PipelineHistoryStatusEnum $status): void
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

    public function setStepHistories(iterable $stepHistories): void
    {
        $this->stepHistories = $stepHistories;
        foreach ($stepHistories as $stepHistory) {
            $stepHistory->setPipelineHistory($this);
        }
    }

    public function setPipeline(Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }
}
