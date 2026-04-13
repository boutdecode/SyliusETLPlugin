<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_grid', [
        'templates' => [
            'action' => [
                'transitionButton' => '@BoutDeCodeSyliusETLPlugin/admin/grid/action/transition-button.html.twig',
            ],
        ],
    ]);
};
