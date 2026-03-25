<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Models\Formation;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $formations = [
            [
                'abbreviation' => 'TTR',
                'name' => 'Trinidad and Tobago Regiment',
                'type' => FormationType::Regiment,
                'is_active' => true,
            ],
            [
                'abbreviation' => 'TTCG',
                'name' => 'Trinidad and Tobago Coast Guard',
                'type' => FormationType::CoastGuard,
                'is_active' => true,
            ],
            [
                'abbreviation' => 'TTAG',
                'name' => 'Trinidad and Tobago Air Guard',
                'type' => FormationType::AirGuard,
                'is_active' => true,
            ],
            [
                'abbreviation' => 'TTDFR',
                'name' => 'Trinidad and Tobago Defence Force Reserve',
                'type' => FormationType::Reserve,
                'is_active' => true,
            ],
        ];

        foreach ($formations as $formation) {
            Formation::updateOrCreate(
                ['abbreviation' => $formation['abbreviation']],
                $formation,
            );
        }
    }
}
