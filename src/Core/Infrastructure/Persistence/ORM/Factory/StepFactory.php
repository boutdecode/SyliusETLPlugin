<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory;

use Akawaka\ETLCoreBundle\Core\Domain\Factory\StepFactory as CoreStepFactory;
use Akawaka\ETLCoreBundle\Core\Domain\Model\Step as CoreStep;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Step;

class StepFactory implements CoreStepFactory
{

    public function create(
        string $code,
        ?string $name = null,
        array $configuration = [],
        int $order = 0,
    ): CoreStep
    {
        $step = new Step();
        $step->setName($name ?? $code);
        $step->setCode($code);
        $step->setConfiguration($configuration);
        $step->setOrder($order);

        return $step;
    }
}
