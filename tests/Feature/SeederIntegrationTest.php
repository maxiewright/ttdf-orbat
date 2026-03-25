<?php

use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

it('TtdfOrbatSeeder runs without errors', function () {
    expect(fn () => $this->seed(TtdfOrbatSeeder::class))->not()->toThrow(Throwable::class);
});

it('seeder creates exactly 4 formations', function () {
    $this->seed(TtdfOrbatSeeder::class);

    expect(Formation::count())->toBe(4);
});

it('seeder creates rank grades with unique codes', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $codes = RankGrade::pluck('code');

    expect($codes->count())->toBe($codes->unique()->count());
});

it('each formation has 16 ranks', function () {
    $this->seed(TtdfOrbatSeeder::class);

    foreach (['TTR', 'TTCG', 'TTAG'] as $abbreviation) {
        $formation = Formation::where('abbreviation', $abbreviation)->firstOrFail();
        $count = Rank::where('formation_id', $formation->id)->count();

        expect($count)->toBe(16, "{$abbreviation} should have 16 ranks, got {$count}");
    }
});

it('TTR skips OF-D1 grade and covers all others', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

    foreach (RankGrade::where('code', '!=', 'OF-D1')->get() as $grade) {
        expect(Rank::where('formation_id', $formation->id)->where('rank_grade_id', $grade->id)->exists())
            ->toBeTrue("TTR is missing rank for grade {$grade->code}");
    }
});

it('TTCG skips WO-1 grade and covers all others', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTCG')->firstOrFail();

    foreach (RankGrade::where('code', '!=', 'WO-1')->get() as $grade) {
        expect(Rank::where('formation_id', $formation->id)->where('rank_grade_id', $grade->id)->exists())
            ->toBeTrue("TTCG is missing rank for grade {$grade->code}");
    }
});

it('TTAG skips OF-D1 grade and covers all others', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTAG')->firstOrFail();

    foreach (RankGrade::where('code', '!=', 'OF-D1')->get() as $grade) {
        expect(Rank::where('formation_id', $formation->id)->where('rank_grade_id', $grade->id)->exists())
            ->toBeTrue("TTAG is missing rank for grade {$grade->code}");
    }
});

it('TTR has one top-level unit with 4 battalion children', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

    $topLevel = Unit::forFormation($formation)->topLevel()->get();
    expect($topLevel)->toHaveCount(1);
    expect($topLevel->first()->abbreviation)->toBe('TTR');

    $battalions = $topLevel->first()->children;
    expect($battalions)->toHaveCount(4);
});

it('seeder is idempotent — running twice produces same count', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $count = Unit::count();

    $this->seed(TtdfOrbatSeeder::class);

    expect(Unit::count())->toBe($count);
});

it('SFOD is organic to 1st Infantry Battalion', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $sfod = Unit::where('designation', 'SFOD')->firstOrFail();

    expect($sfod->parent->abbreviation)->toBe('1TTR');
});
