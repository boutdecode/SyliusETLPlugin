<?php

declare(strict_types=1);

use BoutDeCode\ETLCoreBundle\Run\Infrastructure\Instrumentation\Logger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public(false);

    $services->load('BoutDeCode\\SyliusETLPlugin\\', '../src/')
        ->exclude('../src/{Migrations,DependencyInjection,BoutDeCodeSyliusETLPlugin.php}');

    $services->set(Logger::class)
        ->arg('$logger', service('monolog.logger.pipeline'));

    // Les interfaces du cœur ETL sont enregistrées comme services abstraits sous Symfony 7.x,
    // ce qui neutralise DataInterfaceAliasPass : on déclare donc les alias explicitement.
    $coreInterfaces = [
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Persister\PipelinePersister' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PipelineRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Persister\PlannedTaskPersister' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PlannedTaskRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Persister\StepPersister' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\StepRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Persister\WorkflowPersister' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Provider\PipelineProvider' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PipelineRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Provider\PlannedTaskProvider' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PlannedTaskRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Data\Provider\WorkflowProvider' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Factory\PipelineFactory' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory\PipelineFactory::class,
        'BoutDeCode\\ETLCoreBundle\\Core\Domain\Factory\WorkflowFactory' => \BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Factory\WorkflowFactory::class,
        'BoutDeCode\\ETLCoreBundle\\Run\Domain\Data\Persister\PipelineHistoryPersister' => \BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository\PipelineHistoryRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Run\Domain\Data\Persister\StepHistoryPersister' => \BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository\StepHistoryRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Run\Domain\Data\Provider\PipelineHistoryProvider' => \BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Repository\PipelineHistoryRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Run\Domain\Factory\PipelineHistoryFactory' => \BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory\PipelineHistoryFactory::class,
        'BoutDeCode\\ETLCoreBundle\\Run\Domain\Factory\StepHistoryFactory' => \BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Persistence\ORM\Factory\StepHistoryFactory::class,
        'BoutDeCode\\ETLCoreBundle\\Run\Domain\Scheduler\ExpressionScheduler' => \BoutDeCode\SyliusETLPlugin\Run\Infrastructure\Schedule\CronExpressionScheduler::class,
        'BoutDeCode\\ETLCoreBundle\\Statistics\Domain\Data\Persister\WorkflowExecutionStatisticPersister' => \BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowExecutionStatisticRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Statistics\Domain\Data\Persister\WorkflowStatisticPersister' => \BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowStatisticRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Statistics\Domain\Data\Provider\WorkflowExecutionStatisticProvider' => \BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowExecutionStatisticRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Statistics\Domain\Data\Provider\WorkflowStatisticProvider' => \BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowStatisticRepository::class,
        'BoutDeCode\\ETLCoreBundle\\Statistics\Domain\Factory\WorkflowExecutionStatisticFactory' => \BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Factory\WorkflowExecutionStatisticFactory::class,
        'BoutDeCode\\ETLCoreBundle\\Statistics\Domain\Factory\WorkflowStatisticFactory' => \BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Factory\WorkflowStatisticFactory::class,
    ];
    foreach ($coreInterfaces as $interface => $implementation) {
        $services->alias($interface, $implementation);
    }

    $services->alias(Application::class, 'console.messenger.application');
};
