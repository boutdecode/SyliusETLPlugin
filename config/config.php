<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('sylius_resource.php');
    $containerConfigurator->import('sylius_grid.php');
    $containerConfigurator->import('twig_hooks/*.yaml');
    $containerConfigurator->import('monolog.php');
};
