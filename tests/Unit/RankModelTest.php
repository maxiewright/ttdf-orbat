<?php

use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

it('fullTitle combines abbreviation and title', function () {
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

    $rank = Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Private',
        'abbreviation' => 'Pte',
    ]);

    expect($rank->full_title)->toBe('Pte (Private)');
});

it('natoCode returns grade nato_code', function () {
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
        'nato_code' => 'OR-1',
    ]);

    $rank = Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Private',
        'abbreviation' => 'Pte',
    ]);

    expect($rank->nato_code)->toBe('OR-1');
});

it('optionsForFormation sorts by grade level', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $grade1 = RankGrade::create([
        'code' => 'OR-1',
        'category' => RankCategory::OtherRanks,
        'level' => 1,
        'label' => 'Private',
    ]);

    $grade2 = RankGrade::create([
        'code' => 'OR-2',
        'category' => RankCategory::OtherRanks,
        'level' => 2,
        'label' => 'Lance Corporal',
    ]);

    Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade2->id,
        'title' => 'Lance Corporal',
        'abbreviation' => 'LCpl',
    ]);

    Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade1->id,
        'title' => 'Private',
        'abbreviation' => 'Pte',
    ]);

    $options = Rank::optionsForFormation($formation->id);

    expect(array_values($options))->toEqual(['Private', 'Lance Corporal']);
});

it('relationships return formation and grade', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $grade = RankGrade::create([
        'code' => 'OR-3',
        'category' => RankCategory::OtherRanks,
        'level' => 3,
        'label' => 'Corporal',
    ]);

    $rank = Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Corporal',
        'abbreviation' => 'Cpl',
    ]);

    expect($rank->formation->abbreviation)->toBe('TTR');
    expect($rank->grade->code)->toBe('OR-3');
});
