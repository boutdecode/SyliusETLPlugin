<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\AbstractStep;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\StepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepRepository::class)]
#[ORM\Table(name: 'etl_step')]
class Step extends AbstractStep
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    protected string $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $code;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Pipeline::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(name: 'pipeline_id', referencedColumnName: 'id', nullable: false)]
    protected Pipeline $pipeline;

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $configuration = [];

    #[ORM\Column(type: 'integer', name: '`order`')]
    protected int $order = 0;

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setPipeline(Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }
}
