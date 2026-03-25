# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel package (`maxiewright/ttdf-orbat`) providing ORBAT (Order of Battle) reference data for the Trinidad and Tobago Defence Force. Built on Spatie's Laravel Package Tools. Currently in early scaffold stage.

## Commands

- **Run tests:** `composer test` (uses Pest 4)
- **Run a single test:** `vendor/bin/pest tests/path/to/TestFile.php` or `vendor/bin/pest --filter="test name"`
- **Format code:** `composer format` (uses Laravel Pint)
- **Static analysis:** `composer analyse` (uses PHPStan/Larastan at level 5)
- **Test with coverage:** `composer test-coverage`

## Architecture

- **Namespace:** `MaxieWright\TtdfOrbat`
- **Service Provider:** `TtdfOrbatServiceProvider` extends Spatie's `PackageServiceProvider` — registers config, views, migrations, and commands via `configurePackage()`
- **Facade:** `MaxieWright\TtdfOrbat\Facades\TtdfOrbat` resolves to `MaxieWright\TtdfOrbat\TtdfOrbat`
- **Testing:** Pest with Orchestra Testbench. Base `TestCase` sets up the package provider and factory name resolution. All tests use `uses(TestCase::class)->in(__DIR__)` via `tests/Pest.php`.
- **Factories:** Placed in `database/factories/` with namespace `MaxieWright\TtdfOrbat\Database\Factories`
- **Migrations:** Stub files in `database/migrations/` (published via `vendor:publish --tag="ttdf-orbat-migrations"`)

## Requirements

- PHP ^8.4
- Laravel 11 or 12
