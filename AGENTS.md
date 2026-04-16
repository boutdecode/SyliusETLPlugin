# SyliusETLPlugin — Agent Instructions

## Project overview

Sylius plugin (`boutdecode/sylius-etl-plugin`) that integrates an ETL pipeline system into the Sylius admin panel. The heavy ETL execution logic lives in the external package `boutdecode/etl-core-bundle`. This plugin provides the Sylius-specific layer: admin UI, Doctrine entities, migrations, and Symfony wiring.

**Namespace:** `BoutDeCode\SyliusETLPlugin`
**Main class:** `src/BoutDeCodeSyliusETLPlugin.php`
**PHP requirement:** `^8.1`

## Repository structure

```
src/
├── BoutDeCodeSyliusETLPlugin.php
├── Core/Infrastructure/Persistence/ORM/
│   ├── Entity/        (Pipeline, Workflow, Step)
│   ├── Factory/
│   └── Repository/
├── DependencyInjection/
├── Migrations/
├── Run/Infrastructure/Persistence/ORM/
│   ├── Entity/        (PipelineHistory, StepHistory)
│   ├── Factory/
│   └── Repository/
└── UI/Admin/
    ├── Form/
    ├── Grid/
    ├── Menu/
    ├── State/Processor/
    └── Twig/
```

## Key conventions

- All PHP files use `declare(strict_types=1)`.
- Entities use Doctrine attribute mappings (`#[ORM\...]`), no XML/YAML.
- Migrations are platform-aware (PostgreSQL, MySQL/MariaDB branches) and must **only** touch `etl_*` tables — never third-party tables such as `messenger_messages`.
- Step services are registered in the consuming application, not in this package. Do not add application-specific step codes (e.g. `etl.loader.sylius_product`) to this package.
- Nullable DB columns must have a matching nullable PHP type (`?Type = null`).

## Development commands

```bash
# Static analysis
vendor/bin/phpstan analyse -c phpstan.neon -l max src/

# Coding standards
vendor/bin/ecs check

# Unit tests
vendor/bin/phpunit

# Behat (non-JS)
vendor/bin/behat --strict --tags="~@javascript&&~@mink:chromedriver"
```

## Dependencies

| Package | Role |
|---|---|
| `boutdecode/etl-core-bundle` | ETL engine (domain models, runner, step registry) |
| `sylius/sylius` | `^1.14` — admin UI framework |
| `symfony/uid` | UUID generation |
| `dragonmantank/cron-expression` | Scheduled pipeline support |
