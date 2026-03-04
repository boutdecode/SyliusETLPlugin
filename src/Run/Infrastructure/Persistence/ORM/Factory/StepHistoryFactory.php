<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Step;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\StepHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Run\Domain\Factory\StepHistoryFactory as CoreStepHistoryFactory;
use BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\StepHistory;

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
