<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Grid;

use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ApplyTransitionAction;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class PipelineGrid extends AbstractGrid implements ResourceAwareGridInterface
{

    public static function getName(): string
    {
        return 'app_admin_pipeline';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('workflow.name')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.workflow')
            )
            ->addField(
                TwigField::create('status', '@BoutDeCodeSyliusETLPlugin/admin/grid/field/status.html.twig')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.status')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.created_at')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('scheduledAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.scheduled_at')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('startedAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.started_at')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('finishedAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.finished_at')
                    ->setSortable(true)
            )
            // Filtres
            ->addFilter(
                StringFilter::create('status')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.status')
            )
            ->addFilter(
                DateFilter::create('createdAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.created_at')
            )
            ->addFilter(
                DateFilter::create('scheduledAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.scheduled_at')
            )
            ->addFilter(
                DateFilter::create('startedAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.started_at')
            )
            ->addFilter(
                DateFilter::create('finishedAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.finished_at')
            )
            // Actions principales
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.create_pipeline')
                )
            )
            // Actions par ligne
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('reset', 'transitionButton')
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.execute')
                        ->setOptions([
                            'link' => [
                                'route' => 'bout_de_code_sylius_etl_plugin_admin_pipeline_reset',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                        ]),
                    ShowAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.show'),
                    DeleteAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.delete')
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Pipeline::class;
    }
}
