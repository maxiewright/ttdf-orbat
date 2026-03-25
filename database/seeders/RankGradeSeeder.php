<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Models\RankGrade;

class RankGradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            // Other Ranks
            ['code' => 'OR-1', 'nato_code' => 'OR-1', 'category' => RankCategory::OtherRanks, 'level' => 1, 'label' => 'Private-equivalent'],
            ['code' => 'OR-2', 'nato_code' => 'OR-2', 'category' => RankCategory::OtherRanks, 'level' => 2, 'label' => 'Lance Corporal-equivalent'],
            ['code' => 'OR-3', 'nato_code' => 'OR-3', 'category' => RankCategory::OtherRanks, 'level' => 3, 'label' => 'Corporal-equivalent'],
            ['code' => 'OR-4', 'nato_code' => 'OR-4', 'category' => RankCategory::OtherRanks, 'level' => 4, 'label' => 'Sergeant-equivalent'],
            ['code' => 'OR-5', 'nato_code' => 'OR-5', 'category' => RankCategory::OtherRanks, 'level' => 5, 'label' => 'Staff Sergeant-equivalent'],
            ['code' => 'OR-6', 'nato_code' => 'OR-6', 'category' => RankCategory::OtherRanks, 'level' => 6, 'label' => 'Colour Sergeant-equivalent (reserve)'],
            ['code' => 'OR-7', 'nato_code' => 'OR-7', 'category' => RankCategory::OtherRanks, 'level' => 7, 'label' => '(reserve grade)'],

            // Warrant Officers
            ['code' => 'WO-1', 'nato_code' => 'OR-8', 'category' => RankCategory::Warrant, 'level' => 1, 'label' => 'Warrant Officer Class II-equivalent'],
            ['code' => 'WO-2', 'nato_code' => 'OR-9', 'category' => RankCategory::Warrant, 'level' => 2, 'label' => 'Warrant Officer Class I-equivalent'],

            // Officers
            ['code' => 'OF-1D', 'nato_code' => 'OF-D', 'category' => RankCategory::Commissioned, 'level' => 1, 'label' => 'Officer Cadet / Student Officer'],
            ['code' => 'OF-1', 'nato_code' => 'OF-1', 'category' => RankCategory::Commissioned, 'level' => 2, 'label' => 'Second Lieutenant-equivalent'],
            ['code' => 'OF-2', 'nato_code' => 'OF-2', 'category' => RankCategory::Commissioned, 'level' => 3, 'label' => 'Lieutenant-equivalent'],
            ['code' => 'OF-3', 'nato_code' => 'OF-3', 'category' => RankCategory::Commissioned, 'level' => 4, 'label' => 'Captain-equivalent'],
            ['code' => 'OF-4', 'nato_code' => 'OF-4', 'category' => RankCategory::Commissioned, 'level' => 5, 'label' => 'Major-equivalent'],
            ['code' => 'OF-5', 'nato_code' => 'OF-5', 'category' => RankCategory::Commissioned, 'level' => 6, 'label' => 'Lieutenant Colonel-equivalent'],
            ['code' => 'OF-6', 'nato_code' => 'OF-6', 'category' => RankCategory::Commissioned, 'level' => 7, 'label' => 'Colonel-equivalent'],
            ['code' => 'OF-7', 'nato_code' => 'OF-7', 'category' => RankCategory::Commissioned, 'level' => 8, 'label' => 'Brigadier-equivalent'],
            ['code' => 'OF-8', 'nato_code' => 'OF-8', 'category' => RankCategory::Commissioned, 'level' => 9, 'label' => 'Major General-equivalent'],
        ];

        foreach ($grades as $grade) {
            RankGrade::updateOrCreate(
                ['code' => $grade['code']],
                $grade,
            );
        }
    }
}
