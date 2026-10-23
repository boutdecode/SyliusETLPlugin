<?php

declare(strict_types=1);

namespace Tests\BoutDeCode\SyliusETLPlugin\Unit\UI\Admin\State\Processor;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Persister\PipelinePersister;
use BoutDeCode\ETLCoreBundle\Core\Domain\Factory\PipelineFactory;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PipelineData;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor\CreatePipelineProcessor;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Create;

final class CreatePipelineProcessorTest extends TestCase
{
    public function testItCreatesAPlannedPipelineFromTheWorkflow(): void
    {
        $workflow = new Workflow('Workflow');
        $scheduledAt = new \DateTimeImmutable('+1 hour');

        $data = new PipelineData();
        $data->name = 'Run';
        $data->workflow = $workflow;
        $data->configuration = ['override' => true];
        $data->input = ['a' => 1];
        $data->scheduledAt = $scheduledAt;

        $pipeline = $this->createMock(Pipeline::class);
        $pipeline->expects(self::once())->method('plan')->with($scheduledAt);

        $factory = $this->createMock(PipelineFactory::class);
        $factory->expects(self::once())->method('createFromWorkflow')
            ->with($workflow, 'Run', ['override' => true], ['a' => 1])
            ->willReturn($pipeline);

        $persister = $this->createMock(PipelinePersister::class);
        $persister->expects(self::once())->method('create')->with($pipeline)->willReturnArgument(0);

        $processor = new CreatePipelineProcessor($persister, $factory);

        self::assertSame($pipeline, $processor->process($data, new Create(), new Context()));
    }
}
