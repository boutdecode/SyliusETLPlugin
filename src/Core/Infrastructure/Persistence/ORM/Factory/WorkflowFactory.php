<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory;

use Akawaka\ETLCoreBundle\Core\Domain\Factory\WorkflowFactory as CoreWorkflowFactory;
use Akawaka\ETLCoreBundle\Core\Domain\Model\Workflow as CoreWorkflow;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;

class WorkflowFactory implements CoreWorkflowFactory
{
    public function create(
        string $name,
        array $configuration = [],
        array $stepConfiguration = [],
        ?string $description = null
    ): CoreWorkflow
    {
        $workflow = new Workflow();
        $workflow->setName($name);
        $workflow->setConfiguration($configuration);
        $workflow->setStepConfiguration($stepConfiguration);
        $workflow->setDescription($description);

        return $workflow;
    }
}
