<?php

declare(strict_types=1);

use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\PlannedTaskResource;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\PlannedTaskType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\PlannedTaskGrid;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor\SavePlannedTaskProcessor;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Factory\PlannedTaskDataFactory;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider\PlannedTaskDataProvider;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PlannedTaskData;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider\EntityProvider;
use Sylius\Resource\Doctrine\Common\State\RemoveProcessor;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return new ResourceMetadata(
    alias: 'bout_de_code_sylius_etl_plugin.planned_task',
    section: 'admin',
    templatesDir: '@SyliusAdmin/shared/crud',
    routePrefix: '/admin',
    name: 'planned_task',
    class: PlannedTaskResource::class,
    operations: [
        new Index(
            grid: PlannedTaskGrid::class,
        ),
        new Create(
            factory: PlannedTaskDataFactory::class,
            factoryMethod: 'createNew',
            processor: SavePlannedTaskProcessor::class,
            formType: PlannedTaskType::class,
            formOptions: ['data_class' => PlannedTaskData::class],
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_planned_task_index',
        ),
        new Update(
            provider: PlannedTaskDataProvider::class,
            processor: SavePlannedTaskProcessor::class,
            formType: PlannedTaskType::class,
            formOptions: ['data_class' => PlannedTaskData::class],
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_planned_task_index',
        ),
        new Delete(
            provider: EntityProvider::class,
            processor: RemoveProcessor::class,
        ),
        new Show(
            provider: EntityProvider::class,
        ),
    ],
);
