# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel package (`maxiewright/ttdf-orbat`) providing ORBAT (Order of Battle) reference data for the Trinidad and Tobago Defence Force. Built on Spatie's Laravel Package Tools. This is a **package**, not a full application — there is no `app/` directory. PHP ^8.4, Laravel 11 or 12.

## Commands

- **Run tests:** `composer test` (Pest 4 with Orchestra Testbench)
- **Run a single test:** `vendor/bin/pest tests/path/to/TestFile.php` or `vendor/bin/pest --filter="test name"`
- **Format code:** `composer format` (Laravel Pint)
- **Static analysis:** `composer analyse` (PHPStan/Larastan at level 5, scans `src`, `config`, `database`)
- **Test with coverage:** `composer test-coverage`
- **Package discovery after dependency changes:** `composer prepare`

## Architecture

### Namespace & Bootstrap

- **Root namespace:** `MaxieWright\TtdfOrbat` → `src/`
- **Service Provider:** `TtdfOrbatServiceProvider` extends Spatie's `PackageServiceProvider` — registers config, migrations, and commands via `configurePackage()`. Seeders and factories are publishable via `packageBooted()`.
- **Facade:** `MaxieWright\TtdfOrbat\Facades\TtdfOrbat` resolves to `TtdfOrbat` class, which provides 8 cached query methods (formations, ranks, units, tree, etc.). Caching is controlled by `config('ttdf-orbat.cache_ttl')`.

### Models (6 total in `src/Models/`)

- **Formation** — top-level entity (TTR, TTCG, TTAG, TTDFR). Has many Units and Ranks.
- **Unit** — hierarchical via `parent_id` self-reference. Uses `staudenmeir/laravel-adjacency-list` for recursive tree queries (`$unit->descendants`, `$unit->ancestors`, `$unit->breadcrumb`). Supports soft deletes.
- **UnitDetail** — polymorphic (`MorphOne` on Unit) for vessel specs (hull number, displacement, crew) or base specs (location, coordinates). Single table with nullable fields for both types.
- **UnitAttachment** — temporal tracking of unit attachments with `effective_from`/`effective_to` dates and authority references. `current()` scope filters to active attachments.
- **Rank** — belongs to both Formation and RankGrade. Scoped by `officers()` / `otherRanks()`.
- **RankGrade** — 17 cross-service equivalence grades (NATO-style codes like OR-3, OF-4). Category enum: Commissioned/Warrant/OtherRanks.

### Enums (`src/Enums/`)

`FormationType`, `NodeType` (20 unit types with `isNaval()`/`isAir()`/`isArmy()` helpers), `RankCategory`, `ServiceBranch`, `UnitStatus`, `VesselType`. All implement `toArray()` and `label()`.

### Artisan Commands (`src/Commands/`)

- `ttdf-orbat:seed` — runs `TtdfOrbatSeeder` (supports `--fresh` to truncate first)
- `ttdf-orbat:stats` — displays count tables
- `ttdf-orbat:tree {formation}` — ASCII tree of unit hierarchy (`--depth`, `--status` options)
- `ttdf-orbat:validate` — 5 data integrity checks (orphans, rank coverage, attachments, duplicates, empty formations)

### Database

- **Migrations:** 6 `.php.stub` files in `database/migrations/` (published to consumer apps)
- **Seeders:** `MaxieWright\TtdfOrbat\Database\Seeders\` → `database/seeders/`. `TtdfOrbatSeeder` orchestrates all seeders in a transaction. TTR data is complete; TTCG and TTAG unit seeders are not yet implemented.
- **Factories:** `MaxieWright\TtdfOrbat\Database\Factories\` → `database/factories/`. One factory per model with named states (e.g., `UnitFactory::battalion()`, `UnitFactory::vessel()`).

### Testing

Pest 4 with Orchestra Testbench. `TestCase` uses SQLite `:memory:` and manually runs all `.php.stub` migrations. Factory namespace resolution is configured in `TestCase::resolveFactoryName()`. `tests/ArchTest.php` blocks `dd`/`dump`/`ray`. Tests are in `tests/Unit/` and `tests/Feature/`.
