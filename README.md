<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

<h1 align="center">BoutDeCode Sylius ETL Plugin</h1>

<p align="center">Integrate a full ETL (Extract, Transform, Load) pipeline system into the Sylius admin panel.</p>

<p align="center">
    <a href="https://packagist.org/packages/boutdecode/sylius-etlplugin" title="License">
        <img src="https://img.shields.io/packagist/l/boutdecode/sylius-etlplugin.svg" />
    </a>
    <a href="https://packagist.org/packages/boutdecode/sylius-etlplugin" title="PHP Version">
        <img src="https://img.shields.io/packagist/php-v/boutdecode/sylius-etlplugin.svg" />
    </a>
</p>

## Overview

This plugin adds a full-featured ETL workflow management system to the Sylius admin panel. It allows store administrators to define reusable **Workflows** (named chains of ordered ETL Steps), and execute them as **Pipelines** — either manually, via file upload, or on a schedule.

Built-in step types include product import loaders, data transformers, and support for nested workflow execution.

## Features

- **Workflow management** — create and configure reusable chains of ETL steps from the admin panel
- **Pipeline execution** — run workflows as pipelines with JSON input, file upload, or scheduled execution
- **Execution history** — detailed per-step and per-pipeline run history with status tracking
- **Built-in step types**:
  - `etl.loader.sylius_product` — create or update Sylius Products and ProductVariants
  - `etl.loader.workflow` — spawn child pipelines from another workflow (chained ETL)
  - `etl.transformer.import_product_mapper` — map flat columnar data (e.g. CSV) to nested product/variant schema
- **State machine** — pipeline lifecycle management with reset/execute transitions
- **Dedicated logging** — separate `pipeline` log channel for ETL activity
- **Sylius admin integration** — ETL section added to the admin sidebar with grid views for Workflows and Pipelines

## Requirements

- PHP `^8.1`
- Sylius `^1.14`
- Symfony `^6.4`
- Symfony Messenger with an `async` transport

## Installation

### 1. Require the plugin via Composer

```bash
composer require boutdecode/sylius-etl-plugin
```

### 2. Enable the plugin

Add the plugin to your `config/bundles.php`:

```php
return [
    // ...
    BoutDeCode\SyliusETLPlugin\BoutDeCodeSyliusETLPlugin::class => ['all' => true],
];
```

### 3. Import the plugin configuration

**PHP** — create or update `config/packages/bout_de_code_sylius_etl.php`:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@BoutDeCodeETLCoreBundle/Resources/config/config.yaml');
    $containerConfigurator->import('@BoutDeCodeSyliusETLPlugin/config/config.php');
};
```

**YAML** — create or update `config/packages/bout_de_code_sylius_etl.yaml`:

```yaml
imports:
    - { resource: '@BoutDeCodeETLCoreBundle/Resources/config/config.yaml' }
    - { resource: '@BoutDeCodeSyliusETLPlugin/config/config.php' }
```

### 4. Import the admin routes

**PHP** — add to `config/routes.php` (or `config/routes/sylius_admin.php`):

```php
<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('@BoutDeCodeSyliusETLPlugin/config/routes/admin.php');
};
```

**YAML** — add to `config/routes.yaml` (or `config/routes/sylius_admin.yaml`):

```yaml
bout_de_code_sylius_etl_admin:
    resource: '@BoutDeCodeSyliusETLPlugin/config/routes/admin.php'
```

### 5. Configure Symfony Messenger

Ensure an `async` transport is configured in `config/packages/messenger.yaml`:

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
```

### 6. Run database migrations

```bash
bin/console doctrine:migrations:migrate
```

## Usage

### Workflows

Navigate to **Admin > ETL > Workflows** to create a new Workflow. A workflow defines an ordered chain of steps, each with a step type code and a JSON configuration.

Available step type codes:

| Code | Type | Description |
|---|---|---|
| `etl.loader.sylius_product` | Loader | Imports products and variants into Sylius |
| `etl.loader.workflow` | Loader | Executes another workflow as a sub-pipeline |
| `etl.transformer.import_product_mapper` | Transformer | Maps flat CSV-style columns to product/variant schema |

### Pipelines

Navigate to **Admin > ETL > Pipelines** to create and execute pipelines from existing workflows.

When creating a pipeline you can provide:
- A **JSON input** payload
- A **file upload** (e.g. a CSV file)
- A **JSON configuration override** to customize step parameters at runtime
- A **scheduled date/time** for deferred execution

### Logging

Pipeline execution is logged to a dedicated file:

```
var/log/{env}_bout_de_code_sylius_etl_plugin.log
```

## Testing

### PHPUnit

```bash
vendor/bin/phpunit
```

### Behat (non-JS scenarios)

```bash
vendor/bin/behat --strict --tags="~@javascript&&~@mink:chromedriver"
```

### Behat (JS scenarios)

1. [Install Symfony CLI](https://symfony.com/download).

2. Start Headless Chrome:

    ```bash
    google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
    ```

3. Start the test application server:

    ```bash
    symfony server:ca:install
    APP_ENV=test symfony server:start --port=8080 --daemon
    ```

4. Run Behat:

    ```bash
    vendor/bin/behat --strict --tags="@javascript,@mink:chromedriver"
    ```

### Static Analysis

```bash
vendor/bin/phpstan analyse -c phpstan.neon -l max src/
```

### Coding Standards

```bash
vendor/bin/ecs check
```

## License

This plugin is released under the [MIT License](LICENSE).
