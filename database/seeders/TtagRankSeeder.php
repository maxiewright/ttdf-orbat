<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

class TtagRankSeeder extends Seeder
{
    public function run(): void
    {
        $formation = Formation::where('abbreviation', 'TTAG')->firstOrFail();

        $ranks = [
            ['grade_code' => 'OR-1D', 'title' => 'Aircraftman Under Training', 'abbreviation' => 'AUT'],
            ['grade_code' => 'OR-1', 'title' => 'Junior Aircraftman', 'abbreviation' => 'JAC'],
            ['grade_code' => 'OR-2', 'title' => 'Senior Aircraftman', 'abbreviation' => 'SAC'],
            ['grade_code' => 'OR-3', 'title' => 'Corporal', 'abbreviation' => 'Cpl'],
            ['grade_code' => 'OR-4', 'title' => 'Sergeant', 'abbreviation' => 'Sgt'],
            ['grade_code' => 'OR-5', 'title' => 'Flight Sergeant', 'abbreviation' => 'FS'],
            // Temporary placeholders covering OR-6/OR-7 until the Air Guard formalises equivalent ranks.
            ['grade_code' => 'OR-6', 'title' => 'Senior Flight Sergeant (placeholder)', 'abbreviation' => 'SFS'],
            ['grade_code' => 'OR-7', 'title' => 'Reserve Senior Flight Sergeant (placeholder)', 'abbreviation' => 'RSF'],
            ['grade_code' => 'WO-1', 'title' => 'Warrant Officer Class II', 'abbreviation' => 'WO2'],
            ['grade_code' => 'WO-2', 'title' => 'Warrant Officer Class I', 'abbreviation' => 'WO1'],
            ['grade_code' => 'OF-1D', 'title' => 'Officer Cadet', 'abbreviation' => 'OCdt'],
            ['grade_code' => 'OF-1', 'title' => 'Pilot Officer', 'abbreviation' => 'Plt Off'],
            ['grade_code' => 'OF-2', 'title' => 'Flying Officer', 'abbreviation' => 'Fg Off'],
            ['grade_code' => 'OF-3', 'title' => 'Flight Lieutenant', 'abbreviation' => 'Flt Lt'],
            ['grade_code' => 'OF-4', 'title' => 'Squadron Leader', 'abbreviation' => 'Sqn Ldr'],
            ['grade_code' => 'OF-5', 'title' => 'Wing Commander', 'abbreviation' => 'Wg Cdr'],
            ['grade_code' => 'OF-6', 'title' => 'Group Captain', 'abbreviation' => 'Gp Capt'],
            ['grade_code' => 'OF-7', 'title' => 'Air Commodore', 'abbreviation' => 'Air Cdre'],
            ['grade_code' => 'OF-8', 'title' => 'Air Vice Marshal', 'abbreviation' => 'AVM'],
        ];

        foreach ($ranks as $rank) {
            $grade = RankGrade::where('code', $rank['grade_code'])->firstOrFail();

            Rank::updateOrCreate(
                ['rank_grade_id' => $grade->id, 'formation_id' => $formation->id],
                ['title' => $rank['title'], 'abbreviation' => $rank['abbreviation']],
            );
        }
    }
}
