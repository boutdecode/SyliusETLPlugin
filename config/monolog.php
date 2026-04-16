<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'channels' => ['pipeline'],
        'handlers' => [
            'pipeline' => [
                'type' => 'rotating_file',
                'channels' => ['pipeline'],
                'path' => '%kernel.logs_dir%/%kernel.environment%_bout_de_code_sylius_etl_plugin.log',
                'max_files' => 30,
            ],
        ],
    ]);
};
