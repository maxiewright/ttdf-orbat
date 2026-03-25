<?php

namespace MaxieWright\TtdfOrbat\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\TtdfOrbatServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MaxieWright\\TtdfOrbat\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            TtdfOrbatServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        foreach (glob(__DIR__.'/../database/migrations/*.php.stub') as $migration) {
            (include $migration)->up();
        }
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
