<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Step as CoreStep;
use BoutDeCode\ETLCoreBundle\Run\Domain\Enum\StepHistoryStatusEnum;
use BoutDeCode\ETLCoreBundle\Run\Domain\Factory\StepHistoryFactory as CoreStepHistoryFactory;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Step;
use BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Entity\StepHistory;
use Webmozart\Assert\Assert;

class StepHistoryFactory implements CoreStepHistoryFactory
{
    public function create(
        CoreStep $step,
        StepHistoryStatusEnum $status,
        mixed $input,
        mixed $result,
    ): StepHistory {
        Assert::isInstanceOf($step, Step::class);

        $stepHistory = new StepHistory();
        $stepHistory->setStep($step);
        $stepHistory->setStatus($status);
        $stepHistory->setInput($input);
        $stepHistory->setResult($result);

        return $stepHistory;
    }
}
