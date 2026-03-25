<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Closure;
use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

class TtrUnitSeeder extends Seeder
{
    public function run(): void
    {
        $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

        $make = function (array $data) use ($formation): Unit {
            $defaults = [
                'formation_id' => $formation->id,
                'service_branch' => ServiceBranch::Army,
                'status' => UnitStatus::Active,
            ];

            $attributes = array_merge($defaults, $data);

            // parent_id is part of the key because abbreviations like "A COY"
            // repeat across battalions. Use --fresh flag to handle reorganisations.
            $key = [
                'formation_id' => $attributes['formation_id'],
                'parent_id' => $attributes['parent_id'] ?? null,
                'abbreviation' => $attributes['abbreviation'],
            ];

            return Unit::updateOrCreate($key, $attributes);
        };

        // ----- Regiment HQ -----
        $rhq = $make([
            'name' => 'Regiment Headquarters',
            'abbreviation' => 'RHQ',
            'node_type' => NodeType::Headquarters,
            'parent_id' => null,
            'sort_order' => 0,
        ]);

        foreach (['G1', 'G2', 'G3', 'G4', 'G5'] as $i => $branch) {
            $make([
                'name' => "{$branch} Branch",
                'abbreviation' => $branch,
                'node_type' => NodeType::Branch,
                'parent_id' => $rhq->id,
                'sort_order' => $i + 1,
            ]);
        }

        // ----- 1st Battalion -----
        $bn1 = $make([
            'name' => '1st Battalion Trinidad and Tobago Regiment',
            'abbreviation' => '1TTR',
            'node_type' => NodeType::Battalion,
            'parent_id' => null,
            'sort_order' => 1,
        ]);

        $this->seedInfantryBattalion($make, $bn1);

        // Tobago Detachment (organic to 1TTR)
        $make([
            'name' => 'Tobago Detachment',
            'abbreviation' => 'TOB DET',
            'node_type' => NodeType::Detachment,
            'parent_id' => $bn1->id,
            'sort_order' => 5,
        ]);

        // ----- 2nd Battalion -----
        $bn2 = $make([
            'name' => '2nd Battalion Trinidad and Tobago Regiment',
            'abbreviation' => '2 TTR',
            'node_type' => NodeType::Battalion,
            'parent_id' => null,
            'sort_order' => 2,
        ]);

        $this->seedInfantryBattalion($make, $bn2);

        // ----- 1st Engineer Battalion -----
        $bn3 = $make([
            'name' => '1st Engineer Battalion',
            'abbreviation' => '1 Engr',
            'node_type' => NodeType::Battalion,
            'parent_id' => null,
            'sort_order' => 3,
        ]);

        $make([
            'name' => 'Headquarters Squadron',
            'abbreviation' => 'HQ SQN',
            'node_type' => NodeType::Company,
            'parent_id' => $bn3->id,
            'sort_order' => 0,
        ]);

        $make([
            'name' => '1 Field Squadron',
            'abbreviation' => '1 FD SQN',
            'node_type' => NodeType::Company,
            'parent_id' => $bn3->id,
            'sort_order' => 1,
        ]);

        $make([
            'name' => '2 Field Squadron',
            'abbreviation' => '2 FD SQN',
            'node_type' => NodeType::Company,
            'parent_id' => $bn3->id,
            'sort_order' => 2,
        ]);

        // ----- Support and Service Battalion -----
        $bn4 = $make([
            'name' => 'Support and Service Battalion',
            'abbreviation' => 'SSB',
            'node_type' => NodeType::Battalion,
            'parent_id' => null,
            'sort_order' => 4,
        ]);

        $make([
            'name' => 'Headquarters Company',
            'abbreviation' => 'HQ COY',
            'node_type' => NodeType::Company,
            'parent_id' => $bn4->id,
            'sort_order' => 0,
        ]);

        $make([
            'name' => 'Ordnance Company',
            'abbreviation' => 'ORD COY',
            'node_type' => NodeType::Company,
            'parent_id' => $bn4->id,
            'sort_order' => 1,
        ]);

        $make([
            'name' => 'Transport Company',
            'abbreviation' => 'TPT COY',
            'node_type' => NodeType::Company,
            'parent_id' => $bn4->id,
            'sort_order' => 2,
        ]);

        $make([
            'name' => 'Catering Company',
            'abbreviation' => 'CAT COY',
            'node_type' => NodeType::Company,
            'parent_id' => $bn4->id,
            'sort_order' => 3,
        ]);
    }

    private function seedInfantryBattalion(Closure $make, Unit $battalion): void
    {
        // HQ Company
        $hqCoy = $make([
            'name' => 'Headquarters Company',
            'abbreviation' => 'HQ COY',
            'node_type' => NodeType::Company,
            'parent_id' => $battalion->id,
            'sort_order' => 0,
        ]);

        $make([
            'name' => 'Signals Department',
            'abbreviation' => 'SIGS DEPT',
            'node_type' => NodeType::Department,
            'parent_id' => $hqCoy->id,
            'sort_order' => 1,
        ]);

        $make([
            'name' => 'Administration Department',
            'abbreviation' => 'ADMIN DEPT',
            'node_type' => NodeType::Department,
            'parent_id' => $hqCoy->id,
            'sort_order' => 2,
        ]);

        $make([
            'name' => 'Motor Transport Department',
            'abbreviation' => 'MT DEPT',
            'node_type' => NodeType::Department,
            'parent_id' => $hqCoy->id,
            'sort_order' => 3,
        ]);

        // Rifle Companies
        $rifleCompanies = [
            ['name' => 'A Company', 'abbreviation' => 'A COY', 'sort_order' => 1],
            ['name' => 'B Company', 'abbreviation' => 'B COY', 'sort_order' => 2],
            ['name' => 'C Company', 'abbreviation' => 'C COY', 'sort_order' => 3],
        ];

        foreach ($rifleCompanies as $coData) {
            $company = $make([
                'name' => $coData['name'],
                'abbreviation' => $coData['abbreviation'],
                'node_type' => NodeType::Company,
                'parent_id' => $battalion->id,
                'sort_order' => $coData['sort_order'],
            ]);

            for ($pl = 1; $pl <= 3; $pl++) {
                $make([
                    'name' => "{$pl} Platoon",
                    'abbreviation' => "{$pl} PL",
                    'node_type' => NodeType::Platoon,
                    'parent_id' => $company->id,
                    'sort_order' => $pl,
                ]);
            }
        }

        // Support Company
        $spCoy = $make([
            'name' => 'Support Company',
            'abbreviation' => 'SP COY',
            'node_type' => NodeType::Company,
            'parent_id' => $battalion->id,
            'sort_order' => 4,
        ]);

        $supportPlatoons = [
            ['name' => 'Mortar Platoon', 'abbreviation' => 'MOR PL', 'sort_order' => 1],
            ['name' => 'Reconnaissance Platoon', 'abbreviation' => 'RECCE PL', 'sort_order' => 2],
            ['name' => 'Pioneer Platoon', 'abbreviation' => 'PNR PL', 'sort_order' => 3],
        ];

        foreach ($supportPlatoons as $plData) {
            $make([
                'name' => $plData['name'],
                'abbreviation' => $plData['abbreviation'],
                'node_type' => NodeType::Platoon,
                'parent_id' => $spCoy->id,
                'sort_order' => $plData['sort_order'],
            ]);
        }
    }
}
