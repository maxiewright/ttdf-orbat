<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

class TtcgRankSeeder extends Seeder
{
    public function run(): void
    {
        $formation = Formation::where('abbreviation', 'TTCG')->firstOrFail();

        $ranks = [
            // Other Ranks
            ['grade_code' => 'OR-1', 'title' => 'Ordinary Seaman', 'abbreviation' => 'OS'],
            ['grade_code' => 'OR-2', 'title' => 'Able Seaman', 'abbreviation' => 'AB'],
            ['grade_code' => 'OR-3', 'title' => 'Leading Seaman', 'abbreviation' => 'LS'],
            ['grade_code' => 'OR-4', 'title' => 'Petty Officer', 'abbreviation' => 'PO'],
            ['grade_code' => 'OR-5', 'title' => 'Chief Petty Officer', 'abbreviation' => 'CPO'],
            // Warrant Officers — TTCG has no WO-1 equivalent; FCPO maps to WO-2 (OR-9)
            ['grade_code' => 'WO-2', 'title' => 'Fleet Chief Petty Officer', 'abbreviation' => 'FCPO'],
            // Officers
            ['grade_code' => 'OF-D1', 'title' => 'Officer Cadet', 'abbreviation' => 'OCdt'],
            ['grade_code' => 'OF-D', 'title' => 'Midshipman', 'abbreviation' => 'Mid'],
            ['grade_code' => 'OF-1', 'title' => 'Acting Sub-Lieutenant', 'abbreviation' => 'A/SLt'],
            ['grade_code' => 'OF-2', 'title' => 'Sub-Lieutenant', 'abbreviation' => 'SLt'],
            ['grade_code' => 'OF-3', 'title' => 'Lieutenant', 'abbreviation' => 'Lt'],
            ['grade_code' => 'OF-4', 'title' => 'Lieutenant Commander', 'abbreviation' => 'Lt Cdr'],
            ['grade_code' => 'OF-5', 'title' => 'Commander', 'abbreviation' => 'Cdr'],
            ['grade_code' => 'OF-6', 'title' => 'Captain', 'abbreviation' => 'Capt(N)'],
            ['grade_code' => 'OF-7', 'title' => 'Commodore', 'abbreviation' => 'Cdre'],
            ['grade_code' => 'OF-8', 'title' => 'Rear Admiral', 'abbreviation' => 'RAdm'],
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
