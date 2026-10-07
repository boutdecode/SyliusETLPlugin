<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Sidebar + navbar de l'admin Sylius 2 pour les pages propres au plugin (show, dashboard).
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'bout_de_code_etl.page' => [
                'sidebar' => [
                    'template' => '@SyliusAdmin/shared/crud/common/sidebar.html.twig',
                    'priority' => 200,
                ],
                'navbar' => [
                    'template' => '@SyliusAdmin/shared/crud/common/navbar.html.twig',
                    'priority' => 100,
                ],
            ],
        ],
    ]);
};
