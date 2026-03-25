<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;
use MaxieWright\TtdfOrbat\Models\UnitDetail;

it('formations table has expected columns', function () {
    foreach (['id', 'name', 'abbreviation', 'type', 'is_active', 'created_at', 'updated_at'] as $column) {
        expect(Schema::hasColumn('formations', $column))->toBeTrue();
    }
});

it('rank_grades code column is unique', function () {
    RankGrade::create([
        'code' => 'OR-1',
        'category' => 'OR',
        'level' => 1,
        'label' => 'Private',
    ]);

    expect(fn () => RankGrade::create([
        'code' => 'OR-1',
        'category' => 'OR',
        'level' => 2,
        'label' => 'Another Private',
    ]))->toThrow(QueryException::class);
});

it('ranks enforces unique grade per formation', function () {
    $formation = Formation::create([
        'name' => 'Test',
        'abbreviation' => 'TST',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $grade = RankGrade::create([
        'code' => 'OR-1D',
        'category' => 'OR',
        'level' => 0,
        'label' => 'Recruit',
    ]);

    Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Recruit',
        'abbreviation' => 'Rec',
    ]);

    expect(fn () => Rank::create([
        'formation_id' => $formation->id,
        'rank_grade_id' => $grade->id,
        'title' => 'Duplicate',
        'abbreviation' => 'Dup',
    ]))->toThrow(QueryException::class);
});

it('units table has soft deletes', function () {
    expect(Schema::hasColumn('units', 'deleted_at'))->toBeTrue();
});

it('unit_details uses morph columns', function () {
    expect(Schema::hasColumn('unit_details', 'detailable_type'))->toBeTrue();
    expect(Schema::hasColumn('unit_details', 'detailable_id'))->toBeTrue();
});

it('hull_number allows duplicate values', function () {
    $formation = Formation::create([
        'name' => 'Marine',
        'abbreviation' => 'MRN',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $unit = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Unit,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Test Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ]);

    $otherUnit = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Unit,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Other Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 1,
    ]);

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'hull_number' => 'H-1',
    ]);

    $second = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $otherUnit->id,
        'hull_number' => 'H-1',
    ]);

    expect($second)->toBeInstanceOf(UnitDetail::class);
});

it('unit_attachments effective_to is nullable', function () {
    $formation = Formation::create([
        'name' => 'Naval',
        'abbreviation' => 'NAV',
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    $unitA = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Unit,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Unit A',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ]);

    $unitB = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Unit,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Unit B',
        'status' => UnitStatus::Active,
        'sort_order' => 1,
    ]);

    $attachment = UnitAttachment::create([
        'unit_id' => $unitA->id,
        'attached_to_id' => $unitB->id,
        'effective_from' => now(),
        'effective_to' => null,
    ]);

    expect($attachment->effective_to)->toBeNull();
});
