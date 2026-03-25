# ORBAT Reference Data for the Trinidad and Tobago Defence Force

This package publishes structured ORBAT (Order of Battle) reference data for the Trinidad and Tobago Defence Force so Laravel applications can seed formations, ranks, units, and related metadata without manually crafting every entry.

## Package Contents

- `src/`: Service provider, facade, enums, models, and console command hooks.
- `database/migrations/*.php.stub`: Migration stubs shipped with the package (formations, rank grades, ranks, units, unit details, unit attachments).
- `database/seeders`: Seeders for formations, ranks, and the Trinidad and Tobago Regiment (TTR) unit tree.
- `config/ttdf-orbat.php`: Package configuration file that publishes via vendor:publish.
- `tests/`: Pest suite covering enums, migrations, models, seeders, and helpers.

## Installation

1. Require the package:
   ```bash
   composer require maxiewright/ttdf-orbat
   ```
2. Publish migrations, config, and seeders (if needed):
   ```bash
   php artisan vendor:publish --tag="ttdf-orbat-migrations"
   php artisan vendor:publish --tag="ttdf-orbat-config"
   php artisan vendor:publish --tag="ttdf-orbat-seeders"
   ```
3. Run the migrations and seed the reference data:
   ```bash
   php artisan migrate
   php artisan db:seed --class="MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder"
   ```

## Usage Examples

- Use the provided enums (`NodeType`, `FormationType`, `RankCategory`, `ServiceBranch`, `UnitStatus`, `VesselType`) to cast columns or to enforce valid values before inserting data.
- Retrieve the TTR formation or its ranks with the models:
  ```php
  $formation = Formation::where('abbreviation', 'TTR')->first();
  $rank = $formation->rankGrades()->first()->titleFor($formation);
  ```
- Attach unit details dynamically via the `UnitDetail` model, and track attachments with `UnitAttachment::current()` and `UnitAttachment::historical()`.
- The `TtdfOrbatCommand` is available via `php artisan ttdf-orbat` for quick sanity checks during development; it echoes `All done`.

## Testing & Static Analysis

Run the Pest suite (recommended with Xdebug when checking coverage):

```bash
./vendor/bin/pest
XDEBUG_MODE=coverage ./vendor/bin/pest --coverage
```

Static analysis and formatting:

```bash
composer analyse
composer format
```

Use `composer prepare` after dependencies change to regenerate Laravel Testbench discovery.

## Development Notes

- The package uses Spatie Laravel Package Tools to register the service provider and migrations.
- Seeders are idempotent and rely on natural keys (abbreviations/codes); repeated runs do not duplicate rows.
- The TTR unit tree seeder currently seeds the Regiment headquarters, four battalions, support elements, and the Tobago Detachment; future seeders for TTCG/TTAG units are stubbed as TODOs.
- Refer to `AGENTS.md` for contributor guidance, directory layout, and command recipes.

## Support & Contribution

Report issues via GitHub issues. Contributions should aim for small, focused commits with descriptive messages such as `Add rank seeders for X formation`. Include test results and coverage notes in your PR description.
