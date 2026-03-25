<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TtdfOrbatSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->command->info('Seeding: FormationSeeder...');
            $this->call(FormationSeeder::class);

            $this->command->info('Seeding: RankGradeSeeder...');
            $this->call(RankGradeSeeder::class);

            $this->command->info('Seeding: TtrRankSeeder...');
            $this->call(TtrRankSeeder::class);

            $this->command->info('Seeding: TtcgRankSeeder...');
            $this->call(TtcgRankSeeder::class);

            $this->command->info('Seeding: TtagRankSeeder...');
            $this->call(TtagRankSeeder::class);

            $this->command->info('Seeding: TtrUnitSeeder...');
            $this->call(TtrUnitSeeder::class);

            // TODO: TtcgUnitSeeder — Coast Guard unit ORBAT
            // TODO: TtagUnitSeeder — Air Guard unit ORBAT
        });
    }
}
