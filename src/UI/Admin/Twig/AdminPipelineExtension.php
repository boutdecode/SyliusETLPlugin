<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Twig;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Pipeline as PipelineEntity;
use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Repository\PipelineRepository;
use BoutDeCode\ETLBundle\Run\Infrastructure\Persistence\ORM\Entity\PipelineHistory;
use BoutDeCode\ETLBundle\Run\Infrastructure\Persistence\ORM\Entity\StepHistory;
use BoutDeCode\ETLBundle\Run\Infrastructure\Persistence\ORM\Repository\PipelineHistoryRepository;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminPipelineExtension extends AbstractExtension
{
    public function __construct(
        private readonly PipelineRepository $pipelineRepository,
        private readonly PipelineHistoryRepository $pipelineHistoryRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('step_configuration', [$this, 'getStepConfiguration']),
            new TwigFunction('workflow_pipelines', [$this, 'getWorkflowPipelines']),
            new TwigFunction('workflow_pipelines_count', [$this, 'countWorkflowPipelines']),
            new TwigFunction('pipeline_histories', [$this, 'getPipelineHistories']),
        ];
    }

    /**
     * @return array<int, PipelineEntity>
     */
    public function getWorkflowPipelines(Workflow $workflow, int $limit = 100): array
    {
        return $this->pipelineRepository->findBy(['workflow' => $workflow], ['createdAt' => 'DESC'], $limit);
    }

    public function countWorkflowPipelines(Workflow $workflow): int
    {
        return $this->pipelineRepository->count(['workflow' => $workflow]);
    }

    /**
     * @return array<int, PipelineHistory>
     */
    public function getPipelineHistories(PipelineEntity $pipeline, int $limit = 100): array
    {
        return [...$this->pipelineHistoryRepository->findByPipeline($pipeline, $limit)];
    }

    /**
     * Configuration of the pipeline step an execution record refers to, with the pipeline overrides.
     *
     * @return array<string, mixed>
     */
    public function getStepConfiguration(Pipeline $pipeline, StepHistory $stepHistory): array
    {
        $step = null;
        foreach ($pipeline->getSteps() as $candidate) {
            if ($candidate->getCode() === $stepHistory->getStepCode() && ($candidate->getName() ?? $candidate->getCode()) === $stepHistory->getStepName()) {
                $step = $candidate;

                break;
            }
        }

        if (null === $step) {
            return [];
        }

        $overrideStepConfiguration = null;
        foreach ($pipeline->getConfiguration() as $config) {
            if (!is_array($config)) {
                continue;
            }

            if (($config['name'] ?? $config['code'] ?? 'unknow') === $step->getName()) {
                $overrideStepConfiguration = $config;

                break;
            }
        }

        /** @var array<string, mixed> $overrideStepConfiguration */
        $overrideStepConfiguration = is_array($overrideStepConfiguration) ? ($overrideStepConfiguration['configuration'] ?? []) : [];

        $result = [];
        foreach ($step->getConfiguration() as $key => $value) {
            if (isset($overrideStepConfiguration[$key])) {
                $result[$key] = [
                    'default' => $value,
                    'override' => $overrideStepConfiguration[$key],
                ];
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
