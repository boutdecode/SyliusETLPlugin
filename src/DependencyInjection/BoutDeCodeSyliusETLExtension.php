<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class BoutDeCodeSyliusETLExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    /** @psalm-suppress UnusedVariable */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);

        // The admin works on the repositories directly: keep the unsecured REST API of etl-bundle off
        // unless the application explicitly enables it.
        $container->prependExtensionConfig('boutdecode_etl', [
            'api' => ['enabled' => false],
        ]);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'BoutDeCode\\SyliusETLPlugin\\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@BoutDeCodeSyliusETLPlugin/src/Migrations';
    }

    /** @return array<string> */
    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }
}
