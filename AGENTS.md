# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel package, not a full application. Core package code lives in `src/`, with models in `src/Models`, enums in `src/Enums`, the service provider in `src/TtdfOrbatServiceProvider.php`, and console entry points in `src/Commands`. Publishable configuration lives in `config/ttdf-orbat.php`. Database assets are package-scoped: migration stubs are in `database/migrations`, seeders in `database/seeders`, and factories in `database/factories`. Tests live in `tests/`, with feature coverage currently centered in `tests/Unit` and architecture checks in `tests/ArchTest.php`. CI and automation live in `.github/workflows`.

## Build, Test, and Development Commands
Use Composer scripts where possible:

- `composer install`: install dependencies.
- `composer prepare`: run Testbench package discovery after dependency changes.
- `composer test`: run the Pest suite.
- `composer test-coverage`: run tests with coverage enabled.
- `composer analyse`: run PHPStan against `src`, `config`, and `database`.
- `composer format`: format PHP code with Laravel Pint.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF endings, 4-space indentation, and trimmed trailing whitespace. Match the PSR-4 namespace `MaxieWright\\TtdfOrbat\\...` to file paths under `src/`. Use PascalCase for classes, enums, and seeders (`TtdfOrbatSeeder`), camelCase for methods and properties, and singular model names (`Rank`, `Formation`). Keep seeders and migrations explicit about the military dataset they manage. Do not leave `dd`, `dump`, or `ray` calls in committed code; `tests/ArchTest.php` blocks them.

## Testing Guidelines
Tests use Pest 4 with Orchestra Testbench. Add tests under `tests/Unit/*Test.php` and prefer clear `it('...')` descriptions. Seed only the data each test needs to keep package tests fast and isolated. Before opening a PR, run `composer test`, `composer analyse`, and `composer format`. Use `composer test-coverage` when changing models, seeders, or package bootstrapping.

## Commit & Pull Request Guidelines
Recent commits use short, imperative, sentence-case subjects such as `Add rank seeders for all formations and TTR unit ORBAT structure`. Follow that pattern and keep each commit focused. Pull requests should summarize behavior changes, call out migration or seeder impact, link the related issue when applicable, and list the verification commands you ran.

## Package-Specific Notes
Treat changes to migration stubs, seeders, and config as consumer-facing. Backward compatibility matters because downstream Laravel apps publish and run these assets.
