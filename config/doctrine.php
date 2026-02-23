<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'orm' => [
            'mappings' => [
                'BoutDeCodeSyliusETLPlugin' => [
                    'type' => 'attribute',
                    'is_bundle' => true,
                    'dir' => 'src',
                    'prefix' => 'BoutDeCode\\SyliusETLPlugin',
                ],
            ],
        ],
    ]);
};
