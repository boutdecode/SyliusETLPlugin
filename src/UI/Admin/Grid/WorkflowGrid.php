<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Grid;

use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class WorkflowGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_admin_workflow';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('name')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.name')
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('description')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.description')
                    ->setSortable(true),
            )
            // Filtres
            ->addFilter(
                StringFilter::create('name')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.name'),
            )
            // Actions principales
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.create_workflow'),
                ),
            )
            // Actions par ligne
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.execute_workflow')
                        ->setOptions([
                            'link' => [
                                'route' => 'bout_de_code_sylius_etl_plugin_admin_pipeline_create',
                                'parameters' => [
                                    'workflowId' => 'resource.id',
                                ],
                            ],
                        ]),
                    UpdateAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.edit'),
                    DeleteAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.delete'),
                ),
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Workflow::class;
    }
}
