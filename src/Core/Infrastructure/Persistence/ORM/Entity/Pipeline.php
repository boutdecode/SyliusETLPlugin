<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Enum\PipelineStatus;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\AbstractPipeline;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PipelineRepository;
use BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\PipelineHistory;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\PipelineType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\PipelineGrid;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor\CreatePipelineProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Symfony\Component\Serializer\Attribute\Ignore;

#[AsResource(
    alias: 'bout_de_code_sylius_etl_plugin.pipeline',
    section: 'admin',
    templatesDir: '@SyliusAdmin/Crud',
    routePrefix: '/admin',
    name: 'pipeline',
    operations: [
        new Index(
            grid: PipelineGrid::class
        ),
        new Create(
            processor: CreatePipelineProcessor::class,
            formType: PipelineType::class,
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_pipeline_index'
        ),
        new Delete(),
        new Show(
            template: '@BoutDeCodeSyliusETLPlugin/admin/pipeline/show.html.twig'
        ),

        new ApplyStateMachineTransition(
            stateMachineComponent: 'symfony',
            stateMachineTransition: 'reset',
            stateMachineGraph: 'pipeline_lifecycle'
        ),
    ]
)]
#[ORM\Entity(repositoryClass: PipelineRepository::class)]
#[ORM\Table(name: 'etl_pipeline')]
class Pipeline extends AbstractPipeline implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    protected string $id;

    /** @var Collection<int, Step> */
    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'pipeline', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['order' => 'ASC'])]
    protected iterable $steps;

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $scheduledAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column(type: 'string', length: 20, enumType: PipelineStatus::class)]
    protected PipelineStatus $status = PipelineStatus::PENDING;

    #[ORM\ManyToOne(targetEntity: Workflow::class)]
    #[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'id')]
    protected CoreWorkflow $workflow;

    #[ORM\OneToMany(targetEntity: PipelineHistory::class, mappedBy: 'pipeline', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    protected Collection $histories;

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $configuration = [];

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $input = [];

    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setSteps(iterable $steps): void
    {
        $this->steps = $steps;
        foreach ($steps as $step) {
            if ($step instanceof Step) {
                $step->setPipeline($this);
            }
        }
    }

    public function setRunnableSteps(iterable $runnableSteps): void
    {
        $this->runnableSteps = $runnableSteps;
        foreach ($runnableSteps as $step) {
            if ($step instanceof Step) {
                $step->setPipeline($this);
            }
        }
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    public function setWorkflow(CoreWorkflow $workflow): void
    {
        $this->workflow = $workflow;
    }

    public function setStatus(PipelineStatus $status): void
    {
        $this->status = $status;
    }

    // Methods for Symfony Workflow component which uses strings
    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatusValue(string $status): void
    {
        $this->status = PipelineStatus::from($status);
    }

    #[Ignore]
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function setScheduledAt(?\DateTimeImmutable $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }
}
