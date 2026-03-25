<?php

use MaxieWright\TtdfOrbat\Database\Seeders\FormationSeeder;
use MaxieWright\TtdfOrbat\Database\Seeders\RankGradeSeeder;
use MaxieWright\TtdfOrbat\Database\Seeders\TtagRankSeeder;
use MaxieWright\TtdfOrbat\Database\Seeders\TtcgRankSeeder;
use MaxieWright\TtdfOrbat\Database\Seeders\TtrRankSeeder;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

it('rank grade codes are unique', function () {
    $this->seed(RankGradeSeeder::class);

    $codes = RankGrade::pluck('code');

    expect($codes)->toHaveCount($codes->unique()->count());
});

it('each formation has the same number of ranks', function () {
    $this->seed([
        FormationSeeder::class,
        RankGradeSeeder::class,
        TtrRankSeeder::class,
        TtcgRankSeeder::class,
        TtagRankSeeder::class,
    ]);

    $counts = collect(['TTR', 'TTCG', 'TTAG'])->map(function (string $abbreviation) {
        $formation = Formation::where('abbreviation', $abbreviation)->first();

        return Rank::where('formation_id', $formation->id)->count();
    });

    expect($counts->unique())->toHaveCount(1, 'All formations should have the same number of ranks');
    expect($counts->first())->toBeGreaterThan(0);
});

it('titleFor returns correct service rank', function () {
    $this->seed([FormationSeeder::class, RankGradeSeeder::class]);

    $grade = RankGrade::where('code', 'OR-3')->firstOrFail();
    $ttr = Formation::where('abbreviation', 'TTR')->firstOrFail();
    $ttcg = Formation::where('abbreviation', 'TTCG')->firstOrFail();

    Rank::create(['rank_grade_id' => $grade->id, 'formation_id' => $ttr->id, 'title' => 'Corporal', 'abbreviation' => 'Cpl']);
    Rank::create(['rank_grade_id' => $grade->id, 'formation_id' => $ttcg->id, 'title' => 'Leading Rating', 'abbreviation' => 'LR']);

    expect($grade->titleFor($ttr)->title)->toBe('Corporal');
    expect($grade->titleFor($ttcg)->title)->toBe('Leading Rating');
});
