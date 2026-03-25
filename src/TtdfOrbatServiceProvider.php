<?php

namespace MaxieWright\TtdfOrbat;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MaxieWright\TtdfOrbat\Commands\TtdfOrbatCommand;

class TtdfOrbatServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ttdf-orbat')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_ttdf_orbat_table')
            ->hasCommand(TtdfOrbatCommand::class);
    }
}
