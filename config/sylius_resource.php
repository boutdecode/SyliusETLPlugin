<?php

declare(strict_types=1);

use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\PipelineResource;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\PlannedTaskResource;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\WorkflowResource;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $resource = static fn (string $model): array => [
        'driver' => false,
        'classes' => [
            'model' => $model,
        ],
    ];

    // The etl-bundle entities are not Sylius resources (no ResourceInterface), so each one is represented by a
    // marker class without Doctrine driver; its operations read and write the entities through custom state
    // providers and processors.
    $containerConfigurator->extension('sylius_resource', [
        'resources' => [
            'bout_de_code_sylius_etl_plugin.workflow' => $resource(WorkflowResource::class),
            'bout_de_code_sylius_etl_plugin.pipeline' => $resource(PipelineResource::class),
            'bout_de_code_sylius_etl_plugin.planned_task' => $resource(PlannedTaskResource::class),
        ],
        'mapping' => [
            'imports' => [
                dirname(__DIR__) . '/config/sylius/resources',
            ],
        ],
    ]);
};
