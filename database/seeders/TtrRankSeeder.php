<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

class TtrRankSeeder extends Seeder
{
    public function run(): void
    {
        $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

        $ranks = [
            ['grade_code' => 'OR-1D', 'title' => 'Recruit', 'abbreviation' => 'Rec'],
            ['grade_code' => 'OR-1', 'title' => 'Private', 'abbreviation' => 'Pte'],
            ['grade_code' => 'OR-2', 'title' => 'Lance Corporal', 'abbreviation' => 'LCpl'],
            ['grade_code' => 'OR-3', 'title' => 'Corporal', 'abbreviation' => 'Cpl'],
            ['grade_code' => 'OR-4', 'title' => 'Sergeant', 'abbreviation' => 'Sgt'],
            ['grade_code' => 'OR-5', 'title' => 'Staff Sergeant', 'abbreviation' => 'SSgt'],
            // Reserve placeholders to keep OR-6/OR-7 coverage until active equivalents exist.
            ['grade_code' => 'OR-6', 'title' => 'Colour Sergeant (Reserve placeholder)', 'abbreviation' => 'C/Sgt'],
            ['grade_code' => 'OR-7', 'title' => 'Reserve Grade (placeholder)', 'abbreviation' => 'Res'],
            ['grade_code' => 'WO-1', 'title' => 'Warrant Officer Class II', 'abbreviation' => 'WO2'],
            ['grade_code' => 'WO-2', 'title' => 'Warrant Officer Class I', 'abbreviation' => 'WO1'],
            ['grade_code' => 'OF-1D', 'title' => 'Officer Cadet', 'abbreviation' => 'OCdt'],
            ['grade_code' => 'OF-1', 'title' => 'Second Lieutenant', 'abbreviation' => '2Lt'],
            ['grade_code' => 'OF-2', 'title' => 'Lieutenant', 'abbreviation' => 'Lt'],
            ['grade_code' => 'OF-3', 'title' => 'Captain', 'abbreviation' => 'Capt'],
            ['grade_code' => 'OF-4', 'title' => 'Major', 'abbreviation' => 'Maj'],
            ['grade_code' => 'OF-5', 'title' => 'Lieutenant Colonel', 'abbreviation' => 'Lt Col'],
            ['grade_code' => 'OF-6', 'title' => 'Colonel', 'abbreviation' => 'Col'],
            ['grade_code' => 'OF-7', 'title' => 'Brigadier', 'abbreviation' => 'Brig'],
            ['grade_code' => 'OF-8', 'title' => 'Major General', 'abbreviation' => 'Maj Gen'],
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
