<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory;

use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\WorkflowFactory as CoreWorkflowFactory;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;

class WorkflowFactory implements CoreWorkflowFactory
{
    public function create(
        string $name,
        array $configuration = [],
        array $stepConfiguration = [],
        ?string $description = null,
    ): CoreWorkflow {
        $workflow = new Workflow();
        $workflow->setName($name);
        $workflow->setConfiguration($configuration);
        $workflow->setStepConfiguration($stepConfiguration);
        $workflow->setDescription($description);

        return $workflow;
    }
}
