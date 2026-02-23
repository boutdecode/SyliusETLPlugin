<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory;

use Akawaka\ETLCoreBundle\Core\Domain\Model\Pipeline;
use Akawaka\ETLCoreBundle\Run\Domain\Enum\PipelineHistoryStatusEnum;
use Akawaka\ETLCoreBundle\Run\Domain\Factory\PipelineHistoryFactory as CorePipelineHistoryFactory;
use Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\PipelineHistory;

class PipelineHistoryFactory implements CorePipelineHistoryFactory
{
    public function create(
        Pipeline $pipeline,
        PipelineHistoryStatusEnum $status,
        array $stepHistories,
        mixed $input,
        mixed $result
    ): PipelineHistory
    {
        $pipelineHistory = new PipelineHistory();
        $pipelineHistory->setPipeline($pipeline);
        $pipelineHistory->setStatus($status);
        $pipelineHistory->setStepHistories($stepHistories);
        $pipelineHistory->setInput($input);
        $pipelineHistory->setResult($result);

        return $pipelineHistory;
    }
}
