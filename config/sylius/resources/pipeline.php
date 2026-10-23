<?php

declare(strict_types=1);

use BoutDeCode\SyliusETLPlugin\UI\Admin\Resource\PipelineResource;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Form\PipelineType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Grid\PipelineGrid;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Processor\CreatePipelineProcessor;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Factory\PipelineDataFactory;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PipelineData;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use BoutDeCode\SyliusETLPlugin\UI\Admin\State\Provider\EntityProvider;
use Sylius\Resource\Doctrine\Common\State\PersistProcessor;
use Sylius\Resource\Doctrine\Common\State\RemoveProcessor;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;

return new ResourceMetadata(
    alias: 'bout_de_code_sylius_etl_plugin.pipeline',
    section: 'admin',
    templatesDir: '@SyliusAdmin/shared/crud',
    routePrefix: '/admin',
    name: 'pipeline',
    class: PipelineResource::class,
    operations: [
        new Index(
            grid: PipelineGrid::class,
        ),
        new Create(
            factory: PipelineDataFactory::class,
            factoryMethod: 'createNew',
            processor: CreatePipelineProcessor::class,
            formType: PipelineType::class,
            formOptions: ['data_class' => PipelineData::class],
            redirectToRoute: 'bout_de_code_sylius_etl_plugin_admin_pipeline_index',
        ),
        new Delete(
            provider: EntityProvider::class,
            processor: RemoveProcessor::class,
        ),
        new Show(
            provider: EntityProvider::class,
        ),
        new ApplyStateMachineTransition(
            provider: EntityProvider::class,
            processor: PersistProcessor::class,
            stateMachineComponent: 'symfony',
            stateMachineTransition: 'reset',
            stateMachineGraph: 'pipeline_lifecycle',
            formType: FormType::class,
            formOptions: ['data_class' => null],
        ),
    ],
);
