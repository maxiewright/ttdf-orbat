<?php

namespace MaxieWright\TtdfOrbat\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use MaxieWright\TtdfOrbat\TtdfOrbatServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MaxieWright\\TtdfOrbat\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            TtdfOrbatServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        foreach (glob(__DIR__ . '/../database/migrations/*.php.stub') as $migration) {
            (include $migration)->up();
        }
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
