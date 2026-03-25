<?php

use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

beforeEach(function () {
    $this->formation = Formation::create([
        'name' => 'Test Formation',
        'abbreviation' => 'TF',
        'type' => 'regiment',
        'is_active' => true,
    ]);
});

it('unit can have children', function () {
    $battalion = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => '1st Battalion',
        'abbreviation' => '1 BN',
        'node_type' => NodeType::Battalion,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
    ]);

    Unit::create([
        'formation_id' => $this->formation->id,
        'name' => 'A Company',
        'abbreviation' => 'A COY',
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
        'parent_id' => $battalion->id,
    ]);

    expect($battalion->children)->toHaveCount(1);
});

it('descendants returns full tree recursively', function () {
    $battalion = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => '1st Battalion',
        'abbreviation' => '1 BN',
        'node_type' => NodeType::Battalion,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
    ]);

    $company = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => 'A Company',
        'abbreviation' => 'A COY',
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
        'parent_id' => $battalion->id,
    ]);

    Unit::create([
        'formation_id' => $this->formation->id,
        'name' => '1 Platoon',
        'abbreviation' => '1 PL',
        'node_type' => NodeType::Platoon,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
        'parent_id' => $company->id,
    ]);

    expect($battalion->descendants)->toHaveCount(2);
});

it('detachment effectiveParent returns attached unit when attached', function () {
    $receivingBn = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => '2nd Battalion',
        'abbreviation' => '2 BN',
        'node_type' => NodeType::Battalion,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
    ]);

    $detachment = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => 'Tobago Detachment',
        'abbreviation' => 'TOB DET',
        'node_type' => NodeType::Detachment,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
    ]);

    UnitAttachment::create([
        'unit_id' => $detachment->id,
        'attached_to_id' => $receivingBn->id,
        'effective_from' => now()->subMonth(),
        'effective_to' => null,
    ]);

    $detachment->load('currentAttachment.attachedTo');

    expect($detachment->effective_parent->id)->toBe($receivingBn->id);
});

it('detachment effectiveParent returns organic parent when not attached', function () {
    $parentBn = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => '1st Battalion',
        'abbreviation' => '1 BN',
        'node_type' => NodeType::Battalion,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
    ]);

    $detachment = Unit::create([
        'formation_id' => $this->formation->id,
        'name' => 'Tobago Detachment',
        'abbreviation' => 'TOB DET',
        'node_type' => NodeType::Detachment,
        'service_branch' => ServiceBranch::Army,
        'status' => UnitStatus::Active,
        'parent_id' => $parentBn->id,
    ]);

    $detachment->load('currentAttachment.attachedTo', 'parent');

    expect($detachment->effective_parent->id)->toBe($parentBn->id);
});

it('vessel scope returns only vessel nodes', function () {
    $defaults = [
        'formation_id' => $this->formation->id,
        'service_branch' => ServiceBranch::Navy,
        'status' => UnitStatus::Active,
    ];

    Unit::create(array_merge($defaults, ['name' => 'TTS Scarborough', 'abbreviation' => 'TTS SCB', 'node_type' => NodeType::Vessel]));
    Unit::create(array_merge($defaults, ['name' => 'A Company', 'abbreviation' => 'A COY', 'node_type' => NodeType::Company]));
    Unit::create(array_merge($defaults, ['name' => '1 Platoon', 'abbreviation' => '1 PL', 'node_type' => NodeType::Platoon]));

    expect(Unit::vessels()->count())->toBe(1);
});

it('active scope excludes decommissioned units', function () {
    $defaults = [
        'formation_id' => $this->formation->id,
        'service_branch' => ServiceBranch::Navy,
        'node_type' => NodeType::Vessel,
    ];

    Unit::create(array_merge($defaults, ['name' => 'TTS Active', 'abbreviation' => 'TTS ACT', 'status' => UnitStatus::Active]));
    Unit::create(array_merge($defaults, ['name' => 'TTS Retired', 'abbreviation' => 'TTS RET', 'status' => UnitStatus::Decommissioned]));

    expect(Unit::active()->count())->toBe(1);
});
