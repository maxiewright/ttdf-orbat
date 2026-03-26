# Changelog

## v0.3.0 — Consumer Traits, Install Command & Complete ORBAT Seeding

### Added

- **`BelongsToOrbat` trait** — mix into any consumer `User` model to gain `rank()`, `unit()`, `formation()`, and `appointment()` `BelongsTo` relationships plus `scopeInUnit()`, `scopeInFormation()`, `scopeWithRank()`, and `scopeWithAppointment()` query scopes. Publish the companion migration with `php artisan ttdf-orbat:install --with-user-fields`
- **`HasOrbatAudience` trait** — mix into any content model (e.g. `Notice`, `Document`, `Event`) to make it audience-targetable by ORBAT entities. Provides `addAudience()`, `removeAudience()`, `isTargetedAt()`, and `scopeForAudience()` targeting `Formation`, `Unit`, or `RankGrade`
- **`OrbatAudience` model** — polymorphic pivot backing the `HasOrbatAudience` trait; `auditable` morphs to the consumer model, `targetable` morphs to the ORBAT entity
- **`create_orbat_audiences_table` migration** — auto-published with core migrations; includes a unique constraint on the four morph columns
- **`ttdf-orbat:install` command** — publishes config and migrations in one step; `--with-user-fields` additionally publishes a migration that adds `formation_id`, `unit_id`, `rank_id`, and `appointment_id` FK columns to the `users` table
- **`TtcgUnitSeeder`** — Coast Guard ORBAT: CG HQ (4 branches), Coast Guard Base (3 departments), Flotilla 1 (6 vessels), and Coast Guard Station Tobago
- **`TtcgAppointmentSeeder`** — appointment templates for Formation, Base, Flotilla, Vessel, Station, and Department node types
- **`TtagUnitSeeder`** — Air Guard ORBAT: AG HQ (3 branches), 1st Air Guard Squadron (3 flights), Technical Wing (2 flights), and Air Guard Station Tobago
- **`TtagAppointmentSeeder`** — appointment templates for Formation, Squadron, Wing, Flight, and Station node types
- **`newFactory()` overrides on all models** — `Formation`, `Unit`, `Rank`, `RankGrade`, `Appointment`, `UnitDetail`, and `UnitAttachment` now explicitly return their package factory, ensuring reliable factory resolution in consumer applications without requiring `guessFactoryNamesUsing` configuration

### Changed

- **`TtrAppointmentSeeder`, `TtcgAppointmentSeeder`, `TtagAppointmentSeeder`** all extend the new `AbstractAppointmentSeeder` — shared `run()` loop and `gradeId()` helper live in one place; each seeder only declares `formationAbbreviation()` and `templates()`
- **`TtrUnitSeeder`, `TtcgUnitSeeder`, `TtagUnitSeeder`** all extend the new `AbstractUnitSeeder` — shared `make()` helper lives in one place; each seeder declares `defaultServiceBranch()` and its own `run()`
- **`HasOrbatAudience`** `targetableKey()` private helper eliminates repeated morph-column filtering across `addAudience()`, `removeAudience()`, `isTargetedAt()`, and `scopeForAudience()`
- **`TtdfOrbatSeeder`** wired up to call all four new seeders in the same transaction as the existing ones

### Upgrade Guide

```bash
composer update maxiewright/ttdf-orbat
php artisan vendor:publish --tag="ttdf-orbat-migrations"
php artisan migrate
php artisan ttdf-orbat:seed
```

The seeder is idempotent — running it on existing data will update changed rows without duplicating.

To link your `User` model to the ORBAT:

```bash
php artisan ttdf-orbat:install --with-user-fields
php artisan migrate
```

Then add `use BelongsToOrbat;` to your `User` model.

---

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
