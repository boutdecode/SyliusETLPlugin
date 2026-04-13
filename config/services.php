<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('services/**');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public(false);

    $services->load('BoutDeCode\\SyliusETLPlugin\\', '../src/')
        ->exclude('../src/{Migrations,DependencyInjection,BoutDeCodeSyliusETLPlugin.php}');
};
