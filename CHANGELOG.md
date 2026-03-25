# Changelog

All notable changes to `ttdf-orbat` will be documented in this file.

## v0.1.0 - 2026-03-25

### Initial Release

**Rank and Unit Reference Data for the Trinidad and Tobago Defence Force**

The first public release of `maxiewright/ttdf-orbat`, a Laravel package providing structured rank and unit reference data for the Trinidad and Tobago Defence Force (TTDF).

#### What's Included

##### Formations

Four TTDF formations seeded and ready to use:

- **TTR** — Trinidad and Tobago Regiment
- **TTCG** — Trinidad and Tobago Coast Guard
- **TTAG** — Trinidad and Tobago Air Guard
- **TTDFR** — Trinidad and Tobago Defence Force Reserve

##### Rank System

Complete rank data for all three active services.

- **17 rank grades** providing cross-service equivalence (NATO-aligned)
- **16 ranks per formation** with titles and abbreviations

##### TTR Unit Tree

The Trinidad and Tobago Regiment ORBAT structure with accurate unit designations:

- **TTR** (Regiment) as top-level unit
  - **1TTR** — 1st Infantry Battalion (RHQ, HQ Coy, Alpha/Bravo/Charlie companies, SFOD)
  - **2TTR** — 2nd Infantry Battalion (HQ Coy, Echo/Foxtrot/Gulf companies, Sp Wpns)
  - **1Engr** — 1st Engineer Battalion (Support Sqn, Field & Construction Sqn, EME Sqn)
  - **SSB** — Support and Service Battalion (HQ, S&T Coy, Maintenance Coy)
  
- Full platoon structure with sequential numbering (1-9 for 1TTR, 13-21 for 2TTR)

##### Models & Relationships

Six Eloquent models with rich scopes, accessors, and relationships:

| Model | Purpose |
|-------|---------|
| `Formation` | Service branches with active/type scopes |
| `RankGrade` | Cross-service rank equivalence with NATO codes |
| `Rank` | Formation-specific titles, abbreviations, seniority ordering |
| `Unit` | Hierarchical tree with adjacency list (ancestors, descendants, breadcrumbs) |
| `UnitDetail` | Vessel specs, base locations, coordinates (polymorphic) |
| `UnitAttachment` | Temporary attachment/detachment tracking with history |

##### Facade API

The `TtdfOrbat` facade provides cached access to all ORBAT data:

```php
TtdfOrbat::formations();          // All active formations
TtdfOrbat::formation('TTR');      // Single formation
TtdfOrbat::ranks('TTR');          // Ranks ordered by seniority
TtdfOrbat::officers('TTCG');      // Officers only
TtdfOrbat::otherRanks('TTAG');    // Enlisted only
TtdfOrbat::tree('TTR');           // Unit tree (top-level with children)
TtdfOrbat::units('TTR');          // All active units (flat)
TtdfOrbat::grades();              // All rank grades
TtdfOrbat::version();             // "0.1.0"

```
##### Artisan Commands

Four commands for managing and inspecting ORBAT data:

- `ttdf-orbat:seed` — Seed all reference data (supports `--fresh`)
- `ttdf-orbat:stats` — Display formation, rank, and unit counts
- `ttdf-orbat:tree TTR` — Render ASCII unit tree (supports `--depth` and `--status`)
- `ttdf-orbat:validate` — Check data integrity (orphans, rank coverage, attachments)

##### Enums

Six string-backed enums for type-safe domain modelling:
`FormationType`, `NodeType`, `RankCategory`, `ServiceBranch`, `UnitStatus`, `VesselType`

##### Model Factories

Factories for all six models with practical states for testing:

```php
Formation::factory()->regiment()->create();
Unit::factory()->battalion()->childOf($parent)->create();
UnitDetail::factory()->forVessel($unit)->create();
UnitAttachment::factory()->ended()->create();

```
Publishable to your application via `vendor:publish --tag=ttdf-orbat-factories`.

##### Test Suite

- **93 tests**, **262 assertions** — all passing
- PHPStan level 5 — zero errors
- Covers models, relationships, scopes, accessors, migrations, seeders, commands, and enums

#### Requirements

- PHP ^8.4
- Laravel 11, 12 or 13

#### Installation

```bash
composer require maxiewright/ttdf-orbat
php artisan vendor:publish --tag="ttdf-orbat-migrations"
php artisan migrate
php artisan ttdf-orbat:seed

```
#### Roadmap

- TTCG unit seeder (vessels, stations, Coast Guard HQ)
- TTAG unit seeder (flights, squadrons, Air Guard HQ)
- TTDFR unit seeders
- Additional TTR units (Training etc)
- Filamentphp integration
