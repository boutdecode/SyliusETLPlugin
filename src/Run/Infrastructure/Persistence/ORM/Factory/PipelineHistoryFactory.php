<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Run\Domain\Factory\PipelineHistoryFactory as CorePipelineHistoryFactory;
use BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\PipelineHistory;

class PipelineHistoryFactory implements CorePipelineHistoryFactory
{
    public function create(
        Pipeline $pipeline,
        PipelineHistoryStatusEnum $status,
        array $stepHistories,
        mixed $input,
        mixed $result,
    ): PipelineHistory {
        $pipelineHistory = new PipelineHistory();
        $pipelineHistory->setPipeline($pipeline);
        $pipelineHistory->setStatus($status);
        $pipelineHistory->setStepHistories($stepHistories);
        $pipelineHistory->setInput($input);
        $pipelineHistory->setResult($result);

        return $pipelineHistory;
    }
}
