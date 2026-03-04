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
        ;

        $subMenu
            ->addChild('workflow', [
                'route' => 'bout_de_code_sylius_etl_plugin_admin_workflow_index',
            ])
            ->setLabelAttribute('icon', 'icon cog')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.workflows')
        ;

        $subMenu
            ->addChild('pipeline', [
                'route' => 'bout_de_code_sylius_etl_plugin_admin_pipeline_index',
            ])
            ->setLabelAttribute('icon', 'icon ellipsis horizontal')
            ->setLabel('bout_de_code_sylius_etl_plugin.ui.pipelines')
        ;
    }
}
