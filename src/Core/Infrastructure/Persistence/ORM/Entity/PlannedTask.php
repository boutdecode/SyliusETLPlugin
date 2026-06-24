<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\AbstractPlannedTask;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline as CorePipeline;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PlannedTaskRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\PlannedTaskType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\PlannedTaskGrid;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

#[AsResource(
    alias: 'bout_de_code_sylius_etl_plugin.planned_task',
    section: 'admin',
    templatesDir: '@SyliusAdmin/Crud',
    routePrefix: '/admin',
    name: 'planned_task',
    operations: [
        new Index(
            grid: PlannedTaskGrid::class,
        ),
        new Create(
            formType: PlannedTaskType::class,
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_planned_task_index',
        ),
        new Update(
            formType: PlannedTaskType::class,
        ),
        new Delete(),
        new Show(
            template: '@BoutDeCodeSyliusETLPlugin/admin/planned_task/show.html.twig',
        ),
    ],
)]
#[ORM\Entity(repositoryClass: PlannedTaskRepository::class)]
#[ORM\Table(name: 'etl_planned_task')]
class PlannedTask extends AbstractPlannedTask implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    protected string $id;

    #[ORM\Column(type: 'boolean')]
    protected bool $enabled;

    #[ORM\ManyToOne(targetEntity: Workflow::class)]
    #[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'id')]
    protected CoreWorkflow $workflow;

    #[ORM\ManyToOne(targetEntity: Pipeline::class)]
    #[ORM\JoinColumn(name: 'pipeline_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    protected CorePipeline|null $pipeline = null;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(type: 'string')]
    protected string $schedule;

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $configuration = [];

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $input = [];

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    protected \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, name: 'updated_at')]
    protected ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setWorkflow(CoreWorkflow $workflow): void
    {
        $this->workflow = $workflow;
    }

    public function setSchedule(string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
