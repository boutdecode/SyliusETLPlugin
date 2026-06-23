<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Schedule;

use BoutDeCode\ETLCoreBundle\Run\Domain\Scheduler\ExpressionScheduler;
use Cron\CronExpression;

class CronExpressionScheduler implements ExpressionScheduler
{
    /** @throws \Exception */
    public function getNextScheduleFromExpression(string $expression): \DateTimeImmutable
    {
        $cronExpression = new CronExpression($expression);

        return \DateTimeImmutable::createFromMutable($cronExpression->getNextRunDate());
    }
}
