<?php

declare(strict_types=1);

use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\WorkflowResource;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\WorkflowType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\WorkflowGrid;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor\SaveWorkflowProcessor;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Factory\WorkflowDataFactory;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider\WorkflowDataProvider;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\WorkflowData;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider\EntityProvider;
use Sylius\Resource\Doctrine\Common\State\RemoveProcessor;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return new ResourceMetadata(
    alias: 'bout_de_code_sylius_etl_plugin.workflow',
    section: 'admin',
    templatesDir: '@SyliusAdmin/shared/crud',
    routePrefix: '/admin',
    name: 'workflow',
    class: WorkflowResource::class,
    operations: [
        new Index(
            grid: WorkflowGrid::class,
        ),
        new Create(
            factory: WorkflowDataFactory::class,
            factoryMethod: 'createNew',
            processor: SaveWorkflowProcessor::class,
            formType: WorkflowType::class,
            formOptions: ['data_class' => WorkflowData::class],
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_workflow_index',
        ),
        new Update(
            provider: WorkflowDataProvider::class,
            processor: SaveWorkflowProcessor::class,
            formType: WorkflowType::class,
            formOptions: ['data_class' => WorkflowData::class],
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_workflow_index',
        ),
        new Show(
            provider: EntityProvider::class,
        ),
        new Delete(
            provider: EntityProvider::class,
            processor: RemoveProcessor::class,
        ),
    ],
);
