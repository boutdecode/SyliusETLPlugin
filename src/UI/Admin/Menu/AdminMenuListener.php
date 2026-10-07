<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'sylius.menu.admin.main', method: 'addAdminMenuItems')]
final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $subMenu = $menu->addChild('etl')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.etl')
            ->setLabelAttribute('icon', 'tabler:cube-spark')
        ;

        $subMenu
            ->addChild('dashboard', [
                'route' => 'bout_de_code_sylius_etl_plugin_admin_dashboard',
            ])
            ->setLabelAttribute('icon', 'tabler:dashboard')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.dashboard')
        ;

        $subMenu
            ->addChild('planned_task', [
                'route' => 'bout_de_code_sylius_etl_plugin_admin_planned_task_index',
            ])
            ->setLabelAttribute('icon', 'tabler:history')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.planned_tasks')
        ;

        $subMenu
            ->addChild('workflow', [
                'route' => 'bout_de_code_sylius_etl_plugin_admin_workflow_index',
            ])
            ->setLabelAttribute('icon', 'tabler:arrows-split')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.workflows')
        ;

        $subMenu
            ->addChild('pipeline', [
                'route' => 'bout_de_code_sylius_etl_plugin_admin_pipeline_index',
            ])
            ->setLabelAttribute('icon', 'tabler:layout-list')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.pipelines')
        ;
    }
}
