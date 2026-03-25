<?php

use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

it('formation can be created with valid data', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    expect($formation->exists)->toBeTrue();
});

it('formation units() only returns top-level units', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $parent = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Battalion,
        'service_branch' => ServiceBranch::Army,
        'name' => '1 TTR',
        'status' => UnitStatus::Active,
        'sort_order' => 1,
    ]);

    Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'A COY',
        'status' => UnitStatus::Active,
        'parent_id' => $parent->id,
        'sort_order' => 1,
    ]);

    expect($formation->units->count())->toBe(1);
});

it('formation allUnits() returns all units regardless of depth', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $parent = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Battalion,
        'service_branch' => ServiceBranch::Army,
        'name' => '1 TTR',
        'status' => UnitStatus::Active,
        'sort_order' => 1,
    ]);

    Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'A COY',
        'status' => UnitStatus::Active,
        'parent_id' => $parent->id,
        'sort_order' => 1,
    ]);

    expect($formation->allUnits->count())->toBe(2);
});

it('shortName accessor returns the abbreviation', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    expect($formation->short_name)->toBe('TTR');
});

it('ranks and rankGrades relations are populated', function () {
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $grade = RankGrade::create([
        'code' => 'OR-1',
        'category' => 'OR',
        'level' => 1,
        'label' => 'Private',
    ]);

    $rank = Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Private',
        'abbreviation' => 'Pte',
    ]);

    expect($formation->ranks->first()->id)->toBe($rank->id);
    expect($formation->rankGrades->pluck('id'))->toContain($grade->id);
});

it('active scope filters inactive formations', function () {
    Formation::create([
        'name' => 'Active',
        'abbreviation' => 'ACT',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    Formation::create([
        'name' => 'Inactive',
        'abbreviation' => 'DIS',
        'type' => FormationType::Regiment,
        'is_active' => false,
    ]);

    expect(Formation::active()->count())->toBe(1);
});

it('ofType scope filters by FormationType', function () {
    Formation::create([
        'name' => 'Regiment',
        'abbreviation' => 'TTR',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    Formation::create([
        'name' => 'Coast Guard',
        'abbreviation' => 'TTCG',
        'type' => FormationType::CoastGuard,
        'is_active' => true,
    ]);

    expect(Formation::ofType(FormationType::CoastGuard)->first()->abbreviation)->toBe('TTCG');
    expect(Formation::ofType(FormationType::Regiment)->count())->toBe(1);
});
