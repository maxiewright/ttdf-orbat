<?php

use Illuminate\Support\Facades\DB;
use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

it('titleFor returns null when formation has no rank for this grade', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $grade = RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    expect($grade->titleFor($formation))->toBeNull();
});

it('titleFor does not execute additional query when ranks already loaded', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $grade = RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Private',
        'abbreviation' => 'Pte',
    ]);

    DB::enableQueryLog();

    $loaded = RankGrade::with('ranks')->find($grade->id);

    DB::flushQueryLog();

    $loaded->titleFor($formation);

    expect(DB::getQueryLog())->toHaveCount(0);

    DB::disableQueryLog();
});

it('officers scope returns only OF category grades', function () {
    RankGrade::create([
        'code' => 'OF-1',
        'category' => RankCategory::Commissioned,
        'level' => 1,
        'label' => 'Second Lieutenant',
    ]);

    RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    expect(RankGrade::officers()->count())->toBe(1);
});

it('warranted scope returns only WO category grades', function () {
    RankGrade::create([
        'code' => 'WO-1',
        'category' => RankCategory::Warrant,
        'level' => 1,
        'label' => 'WO II',
    ]);

    RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    expect(RankGrade::warranted()->count())->toBe(1);
});

it('otherRanks scope returns only OR category grades', function () {
    RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    RankGrade::create([
        'code' => 'OF-1',
        'category' => RankCategory::Commissioned,
        'level' => 1,
        'label' => 'Second Lieutenant',
    ]);

    expect(RankGrade::otherRanks()->count())->toBe(1);
});

it('isOfficer accessor returns true for OF grades', function () {
    $grade = RankGrade::create([
        'code' => 'OF-1',
        'category' => RankCategory::Commissioned,
        'level' => 1,
        'label' => 'Second Lieutenant',
    ]);

    expect($grade->isOfficer)->toBeTrue();
});

it('isOfficer accessor returns false for OR grades', function () {
    $grade = RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    expect($grade->isOfficer)->toBeFalse();
});

it('atLeast scope returns grades at or above given level', function () {
    RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    RankGrade::create([
        'code' => 'OR-2',
        'category' => RankCategory::OtherRanks,
        'level' => 2,
        'label' => 'Lance Corporal',
    ]);

    expect(RankGrade::atLeast(2)->pluck('level')->toArray())->toEqual([2]);
});

it('grades are ordered by level within category', function () {
    RankGrade::create([
        'code' => 'OR-3',
        'category' => RankCategory::OtherRanks,
        'level' => 3,
        'label' => 'Corporal',
    ]);

    RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    RankGrade::create([
        'code' => 'OR-2',
        'category' => RankCategory::OtherRanks,
        'level' => 2,
        'label' => 'Lance Corporal',
    ]);

    $levels = RankGrade::otherRanks()->orderBy('level')->pluck('level')->toArray();

    expect($levels)->toEqual([1, 2, 3]);
});

it('isWarrant accessor returns true for WO grades', function () {
    $grade = RankGrade::create([
        'code' => 'WO-1',
        'category' => RankCategory::Warrant,
        'level' => 1,
        'label' => 'Warrant',
    ]);

    expect($grade->isWarrant)->toBeTrue();
});

it('isEnlisted accessor returns true for OR grades', function () {
    $grade = RankGrade::create([
        'code' => 'OR-2',
        'category' => RankCategory::OtherRanks,
        'level' => 2,
        'label' => 'Lance Corporal',
    ]);

    expect($grade->isEnlisted)->toBeTrue();
});
