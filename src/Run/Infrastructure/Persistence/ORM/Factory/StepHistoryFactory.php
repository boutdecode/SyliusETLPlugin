<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory;

use Akawaka\ETLCoreBundle\Core\Domain\Model\Step;
use Akawaka\ETLCoreBundle\Run\Domain\Enum\StepHistoryStatusEnum;
use Akawaka\ETLCoreBundle\Run\Domain\Factory\StepHistoryFactory as CoreStepHistoryFactory;
use Akawaka\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\StepHistory;

class StepHistoryFactory implements CoreStepHistoryFactory
{
    public function create(
        Step $step,
        StepHistoryStatusEnum $status,
        mixed $input,
        mixed $result
    ): StepHistory
    {
        $stepHistory = new StepHistory();
        $stepHistory->setStep($step);
        $stepHistory->setStatus($status);
        $stepHistory->setInput($input);
        $stepHistory->setResult($result);

        return $stepHistory;
    }
}
