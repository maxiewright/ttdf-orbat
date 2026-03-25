<?php

use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

it('descendants returns all nested children recursively', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $battalion = Unit::where('designation', '1TTR')->firstOrFail();

    $descendants = $battalion->descendants;

    expect($descendants->pluck('abbreviation'))->toContain('A Coy');
    expect($descendants->contains(fn (Unit $unit) => $unit->node_type === NodeType::Platoon))->toBeTrue();
});

it('ancestors returns full chain to root', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $platoon = Unit::where('designation', 'HQACOYACOY1TTR')->firstOrFail();

    $ancestors = $platoon->ancestors;

    expect($ancestors->pluck('abbreviation'))->toContain('A Coy');
    expect($ancestors->pluck('abbreviation'))->toContain('1TTR');
    expect($ancestors->pluck('abbreviation'))->toContain('TTR');
});

it('breadcrumb accessor returns formatted string', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $platoon = Unit::where('designation', '1PLATOONACOY1TTR')->firstOrFail();

    expect($platoon->breadcrumb)->toContain('>');
    expect($platoon->breadcrumb)->toContain('1TTR');
});

it('effectiveParent returns organic parent with no attachment', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $platoon = Unit::where('designation', '1PLATOONACOY1TTR')->firstOrFail();

    expect($platoon->effectiveParent->abbreviation)->toBe('A Coy');
});

it('effectiveParent returns attached unit when active attachment exists', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $sfod = Unit::where('designation', 'SFOD')->firstOrFail();
    $target = Unit::where('designation', '2TTR')->firstOrFail();

    UnitAttachment::create([
        'unit_id' => $sfod->id,
        'attached_to_id' => $target->id,
        'effective_from' => now(),
    ]);

    expect($sfod->fresh()->effectiveParent->abbreviation)->toBe('2TTR');
});

it('forService scope returns only army units for ServiceBranch::Army', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTCG')->firstOrFail();

    $navyUnit = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Vessel,
        'service_branch' => ServiceBranch::Navy,
        'name' => 'Coast Guard Vessel',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ]);

    expect(Unit::forService(ServiceBranch::Army)->pluck('id')->contains($navyUnit->id))->toBeFalse();
    expect(Unit::forService(ServiceBranch::Navy)->pluck('id')->contains($navyUnit->id))->toBeTrue();
});

it('ofType scope returns only vessels', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTCG')->firstOrFail();

    $vessel = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Vessel,
        'service_branch' => ServiceBranch::Navy,
        'name' => 'CG Vessel',
        'status' => UnitStatus::Active,
        'sort_order' => 1,
    ]);

    expect(Unit::ofType(NodeType::Vessel)->pluck('id'))->toContain($vessel->id);
});

it('soft deleted units are excluded from default queries', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

    $unit = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Disposable Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 9,
    ]);

    $unit->delete();

    expect(Unit::find($unit->id))->toBeNull();
});

it('soft deleted units appear with withTrashed()', function () {
    $this->seed(TtdfOrbatSeeder::class);

    $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

    $unit = Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Disposable Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 10,
    ]);

    $unit->delete();

    expect(Unit::withTrashed()->find($unit->id))->not()->toBeNull();
});
