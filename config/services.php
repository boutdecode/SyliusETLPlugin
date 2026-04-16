<?php

declare(strict_types=1);

use BoutDeCode\ETLCoreBundle\Run\Infrastructure\Instrumentation\Logger;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public(false);

    $services->load('BoutDeCode\\SyliusETLPlugin\\', '../src/')
        ->exclude('../src/{Migrations,DependencyInjection,BoutDeCodeSyliusETLPlugin.php}');

    $services->set(Logger::class)
        ->arg('$logger', service('monolog.logger.pipeline'));
};
