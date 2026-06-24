<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Grid;

use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\PlannedTask;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class PlannedTaskGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_admin_planned_task';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('name')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.name'),
            )
            ->addField(
                StringField::create('workflow.name')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.workflow'),
            )
            ->addField(
                TwigField::create('enabled', '@BoutDeCodeSyliusETLPlugin/admin/grid/field/enabled.html.twig')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.enabled'),
            )
            ->addField(
                StringField::create('schedule')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.schedule'),
            )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.created_at'),
            )
            ->addField(
                TwigField::create('pipeline', '@BoutDeCodeSyliusETLPlugin/admin/grid/field/next_schedule.html.twig')
                    ->setLabel('bout_de_code_sylius_etl_plugin.grid.scheduled_at'),
            )

            // Filtres
            ->addFilter(
                StringFilter::create('name')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.name'),
            )
            ->addFilter(
                EntityFilter::create('workflow', Workflow::class)
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.workflow'),
            )
            ->addFilter(
                BooleanFilter::create('enabled')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.enabled'),
            )
            ->addFilter(
                DateFilter::create('created_at')
                    ->setLabel('bout_de_code_sylius_etl_plugin.filter.created_at'),
            )

            // Actions principales
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.create_planned_task'),
                ),
            )
            // Actions par ligne
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create()
                        ->setLabel('bout_de_code_sylius_etl_plugin.action.show'),
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
        return PlannedTask::class;
    }
}
