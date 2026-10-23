# SyliusETLPlugin — Agent Instructions

## Project overview

Sylius plugin (`boutdecode/sylius-etl-plugin`) that integrates an ETL pipeline system into the Sylius admin panel. The ETL engine lives in `boutdecode/etl-core-bundle`; the Doctrine persistence (entities, repositories, factories, `etl_*` tables) lives in `boutdecode/etl-bundle`. This plugin only provides the Sylius-specific layer: admin UI, resource wiring and the schema migration.

**Namespace:** `BoutDeCode\SyliusETLPlugin`
**Main class:** `src/BoutDeCodeSyliusETLPlugin.php`
**PHP requirement:** `^8.2` (Sylius `^2.0`, Doctrine ORM 3)

## Repository structure

```
src/
├── BoutDeCodeSyliusETLPlugin.php
├── DependencyInjection/
├── Migrations/              (single migration aligning the etl_* tables on etl-bundle)
└── UI/Admin/
    ├── Controller/          (dashboard)
    ├── Dto/                 (mutable form models: etl-bundle entities have no setters)
    ├── Form/
    ├── Grid/
    ├── Menu/
    ├── Resource/            (marker classes: Sylius only registers ResourceInterface classes)
    ├── State/{Factory,Processor,Provider}/
    └── Twig/
config/
├── sylius/resources/*.php   (resource operations; PHP files, not #[AsResource])
├── twig_hooks/*.yaml        (Sylius 2 admin hooks: show pages, dashboard layout)
└── routes/admin.php
templates/admin/             (Tabler/Bootstrap, rendered through Twig hooks)
assets/admin/                (React widgets, built by the application's webpack)
```

## Key conventions

- All PHP files use `declare(strict_types=1)`.
- The plugin owns no Doctrine entity. Admin screens read the `etl-bundle` entities and change them only through their factories and `update()`; forms are bound to the DTOs in `UI/Admin/Dto`.
- A Sylius resource is a marker class in `UI/Admin/Resource` (no Doctrine driver). Its operations read and write through `State/Provider` and `State/Processor`, and keep the route names `bout_de_code_sylius_etl_plugin_admin_*`.
- The migration must **only** touch `etl_*` tables — never third-party tables such as `messenger_messages` — and be platform-aware (PostgreSQL, MySQL/MariaDB).
- Step services are registered in the consuming application, not in this package. Do not add application-specific step codes (e.g. `etl.loader.sylius_product`) to this package.

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
| `boutdecode/etl-bundle` | Doctrine persistence, factories, cron scheduler (and an optional REST API, disabled here) |
| `sylius/sylius` | `^2.0` — admin UI framework |
| `symfony/uid` | UUID generation |
