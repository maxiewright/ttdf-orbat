<?php

namespace MaxieWright\TtdfOrbat;

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
            ])
            ->hasCommands([
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
    }
}
