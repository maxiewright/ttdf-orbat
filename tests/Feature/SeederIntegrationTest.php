<?php

use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

it('TtdfOrbatSeeder runs without errors', function () {
    expect(fn () => $this->seed(TtdfOrbatSeeder::class))->not()->toThrow(\Throwable::class);
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

it('TTR has a rank for every grade', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

    foreach (RankGrade::all() as $grade) {
        expect(Rank::where('formation_id', $formation->id)->where('rank_grade_id', $grade->id)->exists())->toBeTrue();
    }
});

it('TTCG has a rank for every grade', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTCG')->firstOrFail();

    foreach (RankGrade::all() as $grade) {
        expect(Rank::where('formation_id', $formation->id)->where('rank_grade_id', $grade->id)->exists())->toBeTrue();
    }
});

it('TTAG has a rank for every grade', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTAG')->firstOrFail();

    foreach (RankGrade::all() as $grade) {
        expect(Rank::where('formation_id', $formation->id)->where('rank_grade_id', $grade->id)->exists())->toBeTrue();
    }
});

it('TTR unit tree contains RHQ and 4 battalions at top level', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

    expect(Unit::forFormation($formation)->topLevel()->count())->toBe(5);
});

it('seeder is idempotent — running twice produces same count', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $count = Formation::count();

    $this->seed(TtdfOrbatSeeder::class);

    expect(Formation::count())->toBe($count);
});

it('Tobago Detachment has organic parent set to 1 TTR', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $detachment = Unit::where('abbreviation', 'TOB DET')->firstOrFail();

    expect($detachment->parent->abbreviation)->toBe('1TTR');
});
