<?php

namespace MaxieWright\TtdfOrbat;

use MaxieWright\TtdfOrbat\Commands\InstallCommand;
use MaxieWright\TtdfOrbat\Commands\SeedCommand;
use MaxieWright\TtdfOrbat\Commands\StatsCommand;
use MaxieWright\TtdfOrbat\Commands\TreeCommand;
use MaxieWright\TtdfOrbat\Commands\ValidateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TtdfOrbatServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ttdf-orbat')
            ->hasConfigFile()
            ->hasMigrations([
                'create_formations_table',
                'create_rank_grades_table',
                'create_ranks_table',
                'create_units_table',
                'create_unit_details_table',
                'create_unit_attachments_table',
                'create_appointments_table',
                'create_orbat_audiences_table',
            ])
            ->hasCommands([
                InstallCommand::class,
                SeedCommand::class,
                StatsCommand::class,
                TreeCommand::class,
                ValidateCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../database/seeders' => database_path('seeders/vendor/ttdf-orbat'),
        ], "{$this->package->shortName()}-seeders");

        $this->publishes([
            __DIR__.'/../database/factories' => database_path('factories/vendor/ttdf-orbat'),
        ], "{$this->package->shortName()}-factories");

        $optionalUserFieldsMigrationPath = $this->getUserFieldsMigrationPath();

        $this->publishes([
            __DIR__.'/../database/migrations/optional/add_orbat_fields_to_users_table.php.stub' => $optionalUserFieldsMigrationPath,
        ], "{$this->package->shortName()}-user-fields");
    }

    /**
     * Determine the destination path for the optional user-fields migration.
     *
     * If a migration ending with "_add_orbat_fields_to_users_table.php" already exists,
     * reuse that path so that republishing is idempotent. Otherwise, create a new
     * timestamped filename in the migrations directory.
     */
    private function getUserFieldsMigrationPath(): string
    {
        $migrationDirectory = database_path('migrations');

        if (is_dir($migrationDirectory)) {
            $existingMigrations = glob($migrationDirectory . '/*_add_orbat_fields_to_users_table.php');

            if (! empty($existingMigrations)) {
                // Reuse the first existing migration file (there should normally be only one).
                return $existingMigrations[0];
            }
        }

        return $migrationDirectory . '/' . date('Y_m_d_His') . '_add_orbat_fields_to_users_table.php';
    }
}
