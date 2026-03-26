# ORBAT Reference Data for the Trinidad and Tobago Defence Force

[![Tests](https://img.shields.io/badge/tests-93%20passing-brightgreen)]()
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen)]()
[![PHP](https://img.shields.io/badge/PHP-%5E8.4-blue)]()
[![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012-blue)]()

A Laravel package providing structured ORBAT (Order of Battle) reference data for the Trinidad and Tobago Defence Force. Covers formations, ranks (all three services), unit trees, vessel details, base locations, attachment tracking, and establishment appointments.

## Installation

```bash
composer require maxiewright/ttdf-orbat
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="ttdf-orbat-migrations"
php artisan migrate
```

Seed the reference data:

```bash
php artisan ttdf-orbat:seed
```

## Publishing Assets

```bash
# Config
php artisan vendor:publish --tag="ttdf-orbat-config"

# Seeders (to customise or extend)
php artisan vendor:publish --tag="ttdf-orbat-seeders"

# Model factories (for testing in your application)
php artisan vendor:publish --tag="ttdf-orbat-factories"
```

## Facade API

The `TtdfOrbat` facade provides cached, convenient access to all ORBAT data:

```php
use MaxieWright\TtdfOrbat\Facades\TtdfOrbat;

// All active formations
TtdfOrbat::formations();

// Single formation by abbreviation
TtdfOrbat::formation('TTR');

// Ranks for a formation (ordered by seniority)
TtdfOrbat::ranks('TTR');

// Filter by category
TtdfOrbat::officers('TTCG');
TtdfOrbat::otherRanks('TTAG');

// All rank grades (cross-service equivalence table)
TtdfOrbat::grades();

// Unit tree (top-level units with children eager-loaded)
TtdfOrbat::tree('TTR');

// All active units for a formation (flat)
TtdfOrbat::units('TTR');

// Appointments for a unit (by abbreviation or model)
TtdfOrbat::appointments('1TTR');
TtdfOrbat::commandAppointments('1TTR');

// Data version
TtdfOrbat::version(); // "0.1.0"
```

Cache TTL is configurable in `config/ttdf-orbat.php`. Set `cache_ttl` to `0` to disable.

## Models

| Model | Purpose |
|-------|---------|
| `Formation` | Service branches (TTR, TTCG, TTAG, TTDFR) |
| `RankGrade` | Cross-service rank equivalence grades (17 grades) |
| `Rank` | Formation-specific rank titles and abbreviations |
| `Unit` | Hierarchical unit tree with adjacency list support |
| `UnitDetail` | Vessel specs, base locations, coordinates (polymorphic) |
| `UnitAttachment` | Temporary attachment/detachment tracking with history |
| `Appointment` | Establishment posts within a unit (the slot, not the holder) |

### Unit Tree Traversal

Units use the `staudenmeir/laravel-adjacency-list` package for recursive queries:

```php
$battalion = Unit::where('abbreviation', '1TTR')->first();

// Full subtree
$battalion->descendants;

// Ancestors to root
$platoon->ancestors;

// Breadcrumb string: "RHQ > 1TTR > A COY > 1 PL"
$platoon->breadcrumb;

// Effective parent (respects active attachments for detachments)
$detachment->effectiveParent;
```

### Appointments

An appointment is a named establishment post within a unit — the *slot*, not the person filling it. Appointments are defined by `NodeType`, and running (or re-running) the seeder applies these templates to all units, including any new units you have added.

```php
use MaxieWright\TtdfOrbat\Models\Unit;

$battalion = Unit::where('abbreviation', '1TTR')->first();

// All appointments for this unit
$battalion->appointments;

// Via facade (cached, eager-loads rank grades)
TtdfOrbat::appointments('1TTR');

// Command appointments only (CO, OC, Pl Comd, etc.)
TtdfOrbat::commandAppointments($battalion);

// Accessors
$appointment->full_title;  // "CO (Commanding Officer)"
$appointment->rank_range;  // "OF-3 – OF-4"
```

#### TTR Formation Appointments

| # | Title | Abbr | Category | Type | Rank Range |
|---|-------|------|----------|------|------------|
| 0 | Commanding Officer | CO | Commissioned | Command | OF-7 – OF-8 |
| 1 | Deputy Commanding Officer | DCO | Commissioned | Command | OF-6 – OF-7 |
| 2 | Senior Staff Officer | SSO | Commissioned | Staff | OF-5 – OF-6 |
| 3 | Personnel Officer | G1 | Commissioned | Staff | OF-4 – OF-5 |
| 4 | Legal Officer | LO | Commissioned | Staff | OF-3 – OF-4 |
| 5 | Human Resource Officer | HRO | Commissioned | Staff | OF-3 – OF-4 |
| 6 | Education Officer | EO | Commissioned | Staff | OF-3 – OF-4 |
| 7 | Intelligence Officer | G2 | Commissioned | Staff | OF-4 – OF-5 |
| 8 | Operations Officer | G3 | Commissioned | Staff | OF-4 – OF-5 |
| 9 | Logistics Officer | G4 | Commissioned | Staff | OF-4 – OF-5 |
| 10 | Projects Officer | G5 | Commissioned | Staff | OF-4 – OF-5 |
| 11 | ICT Officer | G6 | Commissioned | Staff | OF-4 – OF-5 |
| 12 | Regiment Signals Officer | RSO | Commissioned | Staff | OF-4 – OF-5 |
| 13 | Public Relations Officer | G8 | Commissioned | Staff | OF-4 – OF-5 |
| 14 | Welfare Officer | G9 | Commissioned | Staff | OF-4 – OF-5 |
| 15 | Regimental Command Warrant Officer | RCWO | Warrant Officer | Administrative | WO-2 |

## Enums

| Enum | Values |
|------|--------|
| `FormationType` | `regiment`, `coast_guard`, `air_guard`, `reserve`, `joint` |
| `NodeType` | `force`, `formation`, `headquarters`, `battalion`, `company`, `platoon`, `section`, `vessel`, `squadron`, `flight`, `station`, `wing`, `flotilla`, `detachment`, `base`, `installation`, `directorate`, `department`, `branch`, `unit` |
| `RankCategory` | `OF` (commissioned), `WO` (warrant), `OR` (other ranks) |
| `AppointmentType` | `command`, `staff`, `technical`, `administrative`, `medical`, `chaplain`, `legal` |
| `AppointmentCategory` | `commissioned`, `warrant_officer`, `other_ranks`, `civilian` |
| `ServiceBranch` | `army`, `navy`, `air`, `joint` |
| `UnitStatus` | `active`, `reserve`, `decommissioned`, `disbanded` |
| `VesselType` | `opv`, `cpv`, `fpc`, `fcs`, `interceptor` |

## Rank Structure

Ranks are sourced from the [Wikipedia TTDF military ranks](https://en.wikipedia.org/wiki/Military_ranks_of_Trinidad_and_Tobago) reference. The package uses 17 abstract rank grades with formation-specific titles:

| Grade | TTR (Army) | TTCG (Coast Guard) | TTAG (Air Guard) |
|-------|-----------|-------------------|-----------------|
| OF-8 | Major General | Rear Admiral | Air Vice Marshal |
| OF-7 | Brigadier General | Commodore | Air Commodore |
| OF-6 | Colonel | Captain | Group Captain |
| OF-5 | Lieutenant Colonel | Commander | Wing Commander |
| OF-4 | Major | Lieutenant Commander | Squadron Leader |
| OF-3 | Captain | Lieutenant | Flight Lieutenant |
| OF-2 | Lieutenant | Sub-Lieutenant | Flying Officer |
| OF-1 | Second Lieutenant | Acting Sub-Lieutenant | Pilot Officer |
| OF-D | Officer Cadet | Midshipman | Officer Cadet |
| OF-D1 | -- | Officer Cadet | -- |
| OR-5 | Staff Sergeant | Chief Petty Officer | Sergeant |
| OR-4 | Sergeant | Petty Officer | Corporal |
| OR-3 | Corporal | Leading Seaman | Senior Technician |
| OR-2 | Lance Corporal | Able Seaman | Senior Aircraftman |
| OR-1 | Private | Ordinary Seaman | Junior Aircraftman |
| WO-2 | Warrant Officer Class 1 | Fleet Chief Petty Officer | Warrant Officer Class 1 |
| WO-1 | Warrant Officer Class 2 | -- | Warrant Officer Class 2 |

Each formation has 16 ranks (skipping one grade that doesn't apply to their service).

## TTR Unit Tree

The Regiment unit seeder provides:

```
TTR (Trinidad and Tobago Regiment)
├── 1Engr (1st Engineer Battalion)
│   ├── Spt Sqn (Support Squadron)
│   ├── Fld Con Sqn (Field and Construction Squadron)
│   └── EME Sqn (Electrical and Mechanical Engineering Squadron)
├── 1TTR (1st Infantry Battalion)
│   ├── RHQ 1TTR (Regiment Headquarters)
│   ├── HQ 1TTR (Headquarter Company)
│   ├── A Coy (Alpha Company)
│   │   ├── HQ A Coy, 1 Plt, 2 Plt, 3 Plt
│   ├── B Coy (Bravo Company)
│   │   ├── HQ B Coy, 4 Plt, 5 Plt, 6 Plt
│   ├── C Coy (Charlie Company)
│   │   ├── HQ C Coy, 7 Plt, 8 Plt, 9 Plt
│   └── SFOD (Special Forces Operations Detachment)
├── 2TTR (2nd Infantry Battalion)
│   ├── HQ 2TTR (Headquarter Company)
│   ├── E Coy (Echo Company)
│   │   ├── HQ E Coy, 13 Plt, 14 Plt, 15 Plt
│   ├── F Coy (Foxtrot Company)
│   │   ├── HQ F Coy, 16 Plt, 17 Plt, 18 Plt
│   ├── G Coy (Gulf Company)
│   │   ├── HQ G Coy, 19 Plt, 20 Plt, 21 Plt
│   └── Sp Wpns (Support Weapons Operations Detachment)
└── SSB (Support and Service Battalion)
    ├── HQ SSB (Headquarter Company)
    ├── S&T Coy (Supply and Transport Company)
    └── Mn Coy (Maintenance Company)
```

## Artisan Commands

```bash
# Seed ORBAT data (use --fresh to truncate first)
php artisan ttdf-orbat:seed
php artisan ttdf-orbat:seed --fresh

# Display stats summary
php artisan ttdf-orbat:stats

# Render unit tree for a formation
php artisan ttdf-orbat:tree TTR
php artisan ttdf-orbat:tree TTR --depth=2
php artisan ttdf-orbat:tree TTR --status=all

# Validate data integrity
php artisan ttdf-orbat:validate
```

## Model Factories

Factories are available for all models. Use them directly or publish to your application:

```bash
php artisan vendor:publish --tag="ttdf-orbat-factories"
```

```php
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\UnitDetail;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

// Create a formation
$formation = Formation::factory()->regiment()->create();

// Build a unit tree
$bn = Unit::factory()->battalion()->create(['formation_id' => $formation->id]);
$coy = Unit::factory()->company()->childOf($bn)->create();
$plt = Unit::factory()->platoon()->childOf($coy)->create();

// Vessel with details
$vessel = Unit::factory()->vessel()->create(['formation_id' => $formation->id]);
$detail = UnitDetail::factory()->forVessel($vessel)->create();

// Base with coordinates
$base = Unit::factory()->headquarters()->create(['formation_id' => $formation->id]);
$detail = UnitDetail::factory()->forBase($base)->create();

// Rank with grade
$grade = RankGrade::factory()->officer()->create();
$rank = Rank::factory()->create([
    'rank_grade_id' => $grade->id,
    'formation_id' => $formation->id,
]);

// Attachment (current and historical)
$attachment = UnitAttachment::factory()->create([
    'unit_id' => $plt->id,
    'attached_to_id' => $bn->id,
]);
$ended = UnitAttachment::factory()->ended()->create();

// Appointment
use MaxieWright\TtdfOrbat\Models\Appointment;

$co = Appointment::factory()->command()->create([
    'unit_id' => $bn->id,
    'title' => 'Commanding Officer',
    'abbreviation' => 'CO',
]);
```

### Factory States Reference

| Factory | States |
|---------|--------|
| `FormationFactory` | `regiment()`, `coastGuard()`, `airGuard()`, `inactive()` |
| `RankGradeFactory` | `officer()`, `warrant()`, `otherRanks()` |
| `RankFactory` | *(auto-creates grade and formation)* |
| `UnitFactory` | `childOf($parent)`, `battalion()`, `company()`, `platoon()`, `headquarters()`, `vessel()`, `detachment()`, `decommissioned()`, `disbanded()` |
| `UnitDetailFactory` | `forVessel($unit)`, `forBase($unit)` |
| `UnitAttachmentFactory` | `ended()`, `withAuthority($string)` |
| `AppointmentFactory` | `command()`, `inactive()` |

## Testing & Static Analysis

```bash
composer test               # Pest test suite
composer test-coverage      # With coverage report
composer analyse            # PHPStan level 5
composer format             # Laravel Pint
```

## Requirements

- PHP ^8.4
- Laravel 11 or 12

## Upgrading

When updating the package to a new version that includes seeder changes (new appointments, updated unit data, etc.), re-run the seeder to pull in the latest reference data:

```bash
composer update maxiewright/ttdf-orbat

# Publish any new migrations and run them
php artisan vendor:publish --tag="ttdf-orbat-migrations"
php artisan migrate

# Re-seed (idempotent — safe to run on existing data)
php artisan ttdf-orbat:seed

# Or fresh seed (truncates all ORBAT tables first)
php artisan ttdf-orbat:seed --fresh
```

## Roadmap

- TTCG unit seeder (vessels, stations, Coast Guard HQ)
- TTAG unit seeder (flights, squadrons, Air Guard HQ)
- TTCG/TTAG appointment seeders
- TTDFR unit and rank seeders
- Filament admin panel integration

## License

MIT
