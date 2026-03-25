<?php

namespace MaxieWright\TtdfOrbat\Commands;

use Illuminate\Console\Command;
use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;

class SeedCommand extends Command
{
    public $signature = 'ttdf-orbat:seed {--fresh : Truncate all ORBAT tables before seeding}';

    public $description = 'Seed all TTDF ORBAT reference data';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->components->warn('Truncating ORBAT tables...');

            $tables = ['unit_attachments', 'unit_details', 'units', 'ranks', 'rank_grades', 'formations'];

            foreach ($tables as $table) {
                \Illuminate\Support\Facades\DB::table($table)->truncate();
            }
        }

        $this->components->info('Seeding TTDF ORBAT data...');

        $seeder = resolve(TtdfOrbatSeeder::class);
        $seeder->setCommand($this);
        $seeder->__invoke();

        $this->components->info('ORBAT data seeded successfully.');

        return self::SUCCESS;
    }
}
