<?php

use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;
use MaxieWright\TtdfOrbat\Models\UnitDetail;

function makeUnit(array $attributes = []): Unit
{
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => uniqid('TTR'),
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    return Unit::create(array_merge([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ], $attributes));
}

it('relationships expose children, detail, and attachments', function () {
    $parent = makeUnit(['abbreviation' => 'PARENT']);
    $child = makeUnit(['parent_id' => $parent->id, 'abbreviation' => 'CHILD']);

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $parent->id,
        'hull_number' => 'A',
    ]);

    $attachment = UnitAttachment::create([
        'unit_id' => $child->id,
        'attached_to_id' => $parent->id,
        'effective_from' => now(),
    ]);

    expect($parent->children->pluck('id'))->toContain($child->id);
    expect($parent->detail)->toBeInstanceOf(UnitDetail::class);
    expect($parent->attachedUnits->first()->id)->toBe($attachment->id);
    expect($child->attachmentHistory->first()->id)->toBe($attachment->id);
});

it('scopes filter units by status and node type', function () {
    $active = makeUnit(['status' => UnitStatus::Active]);
    $inactive = makeUnit(['status' => UnitStatus::Reserve]);

    makeUnit(['node_type' => NodeType::Vessel, 'abbreviation' => 'VESSEL']);
    makeUnit(['node_type' => NodeType::Detachment, 'abbreviation' => 'DET']);
    makeUnit(['node_type' => NodeType::Department, 'abbreviation' => 'DEPT']);

    expect(Unit::active()->pluck('id')->contains($active->id))->toBeTrue();
    expect(Unit::inactive()->pluck('id')->contains($inactive->id))->toBeTrue();
    expect(Unit::vessels()->pluck('node_type'))->toContain(NodeType::Vessel);
    expect(Unit::detachments()->pluck('node_type'))->toContain(NodeType::Detachment);
    expect(Unit::departments()->pluck('node_type'))->toContain(NodeType::Department);
});

it('accessors provide derived values', function () {
    $unit = makeUnit(['abbreviation' => 'ABBR']);
    $vessel = makeUnit(['node_type' => NodeType::Vessel, 'abbreviation' => 'VESSEL']);
    $detachment = makeUnit(['node_type' => NodeType::Detachment, 'abbreviation' => 'DET']);
    $department = makeUnit(['node_type' => NodeType::Department, 'abbreviation' => 'DEPT']);

    expect($unit->display_name)->toBe('ABBR');
    expect($vessel->isVessel)->toBeTrue();
    expect($detachment->isDetachment)->toBeTrue();
    expect($department->isDepartment)->toBeTrue();

    expect($unit->hasDetail)->toBeFalse();

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'hull_number' => 'X',
    ]);

    expect($unit->fresh()->hasDetail)->toBeTrue();
});
