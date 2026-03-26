# Changelog

All notable changes to `ttdf-orbat` will be documented in this file.

## v0.2.0 — Appointments

### Added

- **Appointment model** — establishment posts within units (the slot, not the holder), with `BelongsTo` relationships to `Unit`, `minRankGrade`, and `maxRankGrade`
- **AppointmentType enum** — `command`, `staff`, `technical`, `administrative`, `medical`, `chaplain`, `legal`
- **AppointmentCategory enum** — `commissioned`, `warrant_officer`, `other_ranks`, `civilian`
- **Migration** `create_appointments_table` with FK constraints, `unique(unit_id, abbreviation)`, and composite indexes
- **AppointmentFactory** with `command()` and `inactive()` states
- **TtrAppointmentSeeder** — seeds appointments by `NodeType` template (Formation, Battalion, Company, Platoon), including G-staff (G1–G6, G8, G9) and specialist appointments (LO, HRO, EO, RSO) at formation level
- **Facade methods** `TtdfOrbat::appointments($unit)` and `TtdfOrbat::commandAppointments($unit)` — accept unit abbreviation string or `Unit` model, return cached collections with eager-loaded rank grades
- **Unit::appointments()** `HasMany` relationship, ordered by `sort_order`
- **Formation::appointments()** `HasManyThrough` relationship via `Unit`
- **Appointment model scopes** — `active()`, `command()`, `forUnit()`, `byCategory()`, `byType()`
- **Appointment model accessors** — `rank_range` (e.g. `"OF-3 – OF-4"`), `full_title` (e.g. `"CO (Commanding Officer)"`)
- **StatsCommand** now reports `Appointments (total)` and `Appointments (command)` counts
- **ValidateCommand** checks for units missing command appointments and appointments with missing/inverted rank grades
- **SeedCommand** `--fresh` now truncates the `appointments` table

### Changed

- `TtdfOrbat` service class — extracted `resolveFormation()` and `resolveUnit()` private helpers to deduplicate formation/unit lookups
- `commandAppointments()` filters `appointments()` in-memory (consistent with `officers()`/`otherRanks()` pattern), eliminating a redundant query and cache entry
- CLAUDE.md expanded with detailed architecture documentation

### Upgrade Guide

```bash
composer update maxiewright/ttdf-orbat
php artisan vendor:publish --tag="ttdf-orbat-migrations"
php artisan migrate
php artisan ttdf-orbat:seed
```

The seeder is idempotent — running it on existing data will update changed rows without duplicating.
