<?php

namespace MaxieWright\TtdfOrbat;

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
            ]);
    }
}
