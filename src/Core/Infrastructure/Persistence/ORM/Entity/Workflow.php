<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\AbstractWorkflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\WorkflowType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\WorkflowGrid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;
use Symfony\Component\Serializer\Attribute\Ignore;

#[AsResource(
    alias: 'bout_de_code_sylius_etl_plugin.workflow',
    section: 'admin',
    templatesDir: '@SyliusAdmin/Crud',
    routePrefix: '/admin',
    name: 'workflow',
    operations: [
        new Index(
            grid: WorkflowGrid::class,
        ),
        new Create(
            formType: WorkflowType::class,
        ),
        new Update(
            formType: WorkflowType::class,
        ),
        new Show(
            template: '@BoutDeCodeSyliusETLPlugin/admin/workflow/show.html.twig',
        ),
        new Delete(),
    ],
)]
#[ORM\Entity(repositoryClass: WorkflowRepository::class)]
#[ORM\Table(name: 'etl_workflow')]
class Workflow extends AbstractWorkflow implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    protected string $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $configuration = [];

    /** @var array<int, mixed> */
    #[ORM\Column(type: 'json', name: 'step_configuration')]
    protected array $stepConfiguration = [];

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    protected \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, name: 'updated_at')]
    protected ?\DateTimeImmutable $updatedAt = null;

    /** @var Collection<int, Pipeline> */
    #[ORM\OneToMany(targetEntity: Pipeline::class, mappedBy: 'workflow')]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    protected Collection $pipelines;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->pipelines = new ArrayCollection();
    }

    /** @return Collection<int, Pipeline> */
    #[Ignore]
    public function getPipelines(): Collection
    {
        return $this->pipelines;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setStepConfiguration(array $stepConfiguration): void
    {
        $this->stepConfiguration = $stepConfiguration;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
