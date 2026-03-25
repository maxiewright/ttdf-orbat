<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

class TtrUnitSeeder extends Seeder
{
    private Formation $formation;

    public function run(): void
    {
        $this->formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

        // ----- Regiment -----
        $ttr = $this->make('TTR', [
            'name' => 'Trinidad and Tobago Regiment',
            'abbreviation' => 'TTR',
            'node_type' => NodeType::Formation,
            'parent_id' => null,
            'sort_order' => 0,
        ]);

        // ----- Battalions -----
        $engr = $this->make('1ENG', [
            'name' => '1st Engineer Battalion',
            'abbreviation' => '1Engr',
            'node_type' => NodeType::Battalion,
            'parent_id' => $ttr->id,
            'sort_order' => 1,
        ]);

        $bn1 = $this->make('1TTR', [
            'name' => '1st Infantry Battalion',
            'abbreviation' => '1TTR',
            'node_type' => NodeType::Battalion,
            'parent_id' => $ttr->id,
            'sort_order' => 2,
        ]);

        $bn2 = $this->make('2TTR', [
            'name' => '2nd Infantry Battalion',
            'abbreviation' => '2TTR',
            'node_type' => NodeType::Battalion,
            'parent_id' => $ttr->id,
            'sort_order' => 3,
        ]);

        $ssb = $this->make('SSB', [
            'name' => 'Support and Service Battalion',
            'abbreviation' => 'SSB',
            'node_type' => NodeType::Battalion,
            'parent_id' => $ttr->id,
            'sort_order' => 4,
        ]);

        // ----- 1st Engineer Battalion sub-units -----
        $this->make('SUPTSQN1ENG', [
            'name' => 'Support Squadron',
            'abbreviation' => 'Spt Sqn',
            'node_type' => NodeType::Squadron,
            'parent_id' => $engr->id,
            'sort_order' => 1,
        ]);

        $this->make('FLDCONSSQN1ENG', [
            'name' => 'Field and Construction Squadron',
            'abbreviation' => 'Fld Con Sqn',
            'node_type' => NodeType::Squadron,
            'parent_id' => $engr->id,
            'sort_order' => 2,
        ]);

        $this->make('EMESQN1ENG', [
            'name' => 'Electrical and Mechanical Engineering Squadron',
            'abbreviation' => 'EME Sqn',
            'node_type' => NodeType::Squadron,
            'parent_id' => $engr->id,
            'sort_order' => 3,
        ]);

        // ----- 1st Infantry Battalion sub-units -----
        $this->make('RHQ', [
            'name' => 'Trinidad and Tobago Regiment Headquarters',
            'abbreviation' => 'RHQ 1TTR',
            'node_type' => NodeType::Headquarters,
            'parent_id' => $bn1->id,
            'sort_order' => 0,
        ]);

        $this->make('HQCOY1TTR', [
            'name' => 'Headquarter Company - 1st Infantry Battalion',
            'abbreviation' => 'HQ 1TTR',
            'node_type' => NodeType::Company,
            'parent_id' => $bn1->id,
            'sort_order' => 1,
        ]);

        $aCoy = $this->make('ACOY1TTR', [
            'name' => 'Alpha Company',
            'abbreviation' => 'A Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $bn1->id,
            'sort_order' => 2,
        ]);

        $bCoy = $this->make('BCOY1TTR', [
            'name' => 'Bravo Company',
            'abbreviation' => 'B Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $bn1->id,
            'sort_order' => 3,
        ]);

        $cCoy = $this->make('CCOY1TTR', [
            'name' => 'Charlie Company',
            'abbreviation' => 'C Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $bn1->id,
            'sort_order' => 4,
        ]);

        $this->make('SFOD', [
            'name' => 'Special Forces Operations Detachment',
            'abbreviation' => 'SFOD',
            'node_type' => NodeType::Detachment,
            'parent_id' => $bn1->id,
            'sort_order' => 5,
        ]);

        // ----- 2nd Infantry Battalion sub-units -----
        $this->make('HQCOY2TTR', [
            'name' => 'Headquarter Company - 2nd Infantry Battalion',
            'abbreviation' => 'HQ 2TTR',
            'node_type' => NodeType::Company,
            'parent_id' => $bn2->id,
            'sort_order' => 0,
        ]);

        $eCoy = $this->make('ECOY2TTR', [
            'name' => 'Echo Company',
            'abbreviation' => 'E Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $bn2->id,
            'sort_order' => 1,
        ]);

        $fCoy = $this->make('FCOY2TTR', [
            'name' => 'Foxtrot Company',
            'abbreviation' => 'F Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $bn2->id,
            'sort_order' => 2,
        ]);

        $gCoy = $this->make('GCOY2TTR', [
            'name' => 'Gulf Company',
            'abbreviation' => 'G Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $bn2->id,
            'sort_order' => 3,
        ]);

        $this->make('SPWPNS', [
            'name' => 'Support Weapons Operations Detachment',
            'abbreviation' => 'Sp Wpns',
            'node_type' => NodeType::Detachment,
            'parent_id' => $bn2->id,
            'sort_order' => 4,
        ]);

        // ----- Support and Service Battalion sub-units -----
        $this->make('HQSSB', [
            'name' => 'Headquarter Company - Support and Service Battalion',
            'abbreviation' => 'HQ SSB',
            'node_type' => NodeType::Company,
            'parent_id' => $ssb->id,
            'sort_order' => 0,
        ]);

        $this->make('STSCOYSSB', [
            'name' => 'Supply and Transport Company - Support and Service Battalion',
            'abbreviation' => 'S&T Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $ssb->id,
            'sort_order' => 1,
        ]);

        $this->make('MNCOYSSB', [
            'name' => 'Maintenance Company - Support and Service Battalion',
            'abbreviation' => 'Mn Coy',
            'node_type' => NodeType::Company,
            'parent_id' => $ssb->id,
            'sort_order' => 2,
        ]);

        // ----- Platoons -----
        $platoons = [
            'ACOY1TTR' => [$aCoy, ['HQ A Coy', '1 Platoon', '2 Platoon', '3 Platoon']],
            'BCOY1TTR' => [$bCoy, ['HQ B Coy', '4 Platoon', '5 Platoon', '6 Platoon']],
            'CCOY1TTR' => [$cCoy, ['HQ C Coy', '7 Platoon', '8 Platoon', '9 Platoon']],
            'ECOY2TTR' => [$eCoy, ['HQ E Coy', '13 Platoon', '14 Platoon', '15 Platoon']],
            'FCOY2TTR' => [$fCoy, ['HQ F Coy', '16 Platoon', '17 Platoon', '18 Platoon']],
            'GCOY2TTR' => [$gCoy, ['HQ G Coy', '19 Platoon', '20 Platoon', '21 Platoon']],
        ];

        foreach ($platoons as $companyCode => [$company, $names]) {
            foreach ($names as $order => $name) {
                $code = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $name.$companyCode));

                $this->make($code, [
                    'name' => $name.' - '.$company->name,
                    'abbreviation' => $this->platoonAbbreviation($name),
                    'node_type' => NodeType::Platoon,
                    'parent_id' => $company->id,
                    'sort_order' => $order,
                ]);
            }
        }
    }

    private function make(string $code, array $data): Unit
    {
        $attributes = array_merge([
            'formation_id' => $this->formation->id,
            'service_branch' => ServiceBranch::Army,
            'status' => UnitStatus::Active,
            'designation' => $code,
        ], $data);

        return Unit::updateOrCreate(
            [
                'formation_id' => $attributes['formation_id'],
                'designation' => $code,
            ],
            $attributes,
        );
    }

    private function platoonAbbreviation(string $name): string
    {
        if (preg_match('/^(\d+)\s+Platoon$/i', $name, $matches)) {
            return $matches[1].' Plt';
        }

        if (preg_match('/^HQ\s+([A-Z])\s+Coy$/i', $name, $matches)) {
            return 'HQ '.strtoupper($matches[1]).' Coy';
        }

        return $name;
    }
}
