<?php

use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitDetail;

function makeUnitForDetail(string $abbreviation = 'TTR'): Unit
{
    $formation = Formation::create([
        'name' => 'Regiment',
        'abbreviation' => $abbreviation,
        'type' => FormationType::Regiment,
        'is_active' => true,
    ]);

    return Unit::create([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Test Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ]);
}

it('pennantNumber formats correctly when both prefix and number present', function () {
    $unit = makeUnitForDetail();

    $detail = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'hull_number' => '41',
        'pennant_prefix' => 'CG',
    ]);

    expect($detail->pennant_number)->toBe('CG-41');
});

it('pennantNumber returns null when hull_number is null', function () {
    $unit = makeUnitForDetail();

    $detail = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'hull_number' => null,
        'pennant_prefix' => 'CG',
    ]);

    expect($detail->pennant_number)->toBeNull();
});

it('pennantNumber returns null when pennant_prefix is null', function () {
    $unit = makeUnitForDetail();

    $detail = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'hull_number' => '41',
        'pennant_prefix' => null,
    ]);

    expect($detail->pennant_number)->toBeNull();
});

it('isActive returns true when decommissioned_at is null', function () {
    $unit = makeUnitForDetail();

    $detail = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'decommissioned_at' => null,
    ]);

    expect($detail->isActive)->toBeTrue();
});

it('isActive returns false when decommissioned_at is set', function () {
    $unit = makeUnitForDetail();

    $detail = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'decommissioned_at' => now()->subYear(),
    ]);

    expect($detail->isActive)->toBeFalse();
});

it('activeVessels scope excludes decommissioned vessels', function () {
    $unit = makeUnitForDetail();

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'hull_number' => 'A',
        'decommissioned_at' => null,
    ]);

    $other = makeUnitForDetail('TTR-ALT');

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $other->id,
        'hull_number' => 'B',
        'decommissioned_at' => now()->subDays(1),
    ]);

    expect(UnitDetail::activeVessels()->pluck('hull_number')->toArray())->toEqualCanonicalizing(['A']);
});

it('bases scope returns entries with base location', function () {
    $unit = makeUnitForDetail('BASE1');

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'base_location' => 'Port-of-Spain',
    ]);

    expect(UnitDetail::bases()->pluck('base_location')->first())->toBe('Port-of-Spain');
});

it('withLocation scope returns entries with coordinates', function () {
    $unit = makeUnitForDetail('LOC1');

    UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'latitude' => 10.0,
        'longitude' => -61.0,
    ]);

    expect((float) UnitDetail::withLocation()->first()->latitude)->toBe(10.0);
});

it('typeLabel returns null safely when vessel_type is null', function () {
    $unit = makeUnitForDetail();

    $detail = UnitDetail::create([
        'detailable_type' => Unit::class,
        'detailable_id' => $unit->id,
        'vessel_type' => null,
    ]);

    expect($detail->type_label)->toBeNull();
});
