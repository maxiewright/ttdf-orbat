<?php

use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

beforeEach(function () {
    $this->seed(TtdfOrbatSeeder::class);
    $this->ttag = Formation::where('abbreviation', 'TTAG')->firstOrFail();
});

it('TTAG has one top-level unit', function () {
    $topLevel = Unit::forFormation($this->ttag)->topLevel()->get();

    expect($topLevel)->toHaveCount(1);
    expect($topLevel->first()->abbreviation)->toBe('TTAG');
});

it('TTAG top-level unit has expected direct children', function () {
    $root = Unit::where('designation', 'TTAG')->firstOrFail();
    $children = $root->children;

    // HQ, 1st Squadron, Technical Wing, Station Tobago
    expect($children)->toHaveCount(4);
});

it('TTAG HQ has 3 branch children', function () {
    $hq = Unit::where('designation', 'AGHQ')->firstOrFail();

    expect($hq->node_type)->toBe(NodeType::Headquarters);
    expect($hq->children)->toHaveCount(3);
    expect($hq->children->every(fn (Unit $u) => $u->node_type === NodeType::Branch))->toBeTrue();
});

it('TTAG 1st Air Guard Squadron has 3 flights', function () {
    $squadron = Unit::where('designation', '1AGS')->firstOrFail();

    expect($squadron->node_type)->toBe(NodeType::Squadron);
    expect($squadron->children)->toHaveCount(3);
    expect($squadron->children->every(fn (Unit $u) => $u->node_type === NodeType::Flight))->toBeTrue();
});

it('TTAG Technical Wing has 2 flights', function () {
    $wing = Unit::where('designation', 'AGTWING')->firstOrFail();

    expect($wing->node_type)->toBe(NodeType::Wing);
    expect($wing->children)->toHaveCount(2);
    expect($wing->children->every(fn (Unit $u) => $u->node_type === NodeType::Flight))->toBeTrue();
});

it('TTAG Air Guard Station Tobago exists as a Station node', function () {
    $station = Unit::where('designation', 'AGST')->firstOrFail();

    expect($station->node_type)->toBe(NodeType::Station);
    expect($station->formation_id)->toBe($this->ttag->id);
});

it('TTAG all units have Air service branch', function () {
    $units = Unit::where('formation_id', $this->ttag->id)->get();

    expect($units)->not()->toBeEmpty();
    expect($units->every(fn (Unit $u) => $u->service_branch->value === 'air'))->toBeTrue();
});

it('TTAG formation node has a Commanding Officer appointment', function () {
    $formationUnit = Unit::where('formation_id', $this->ttag->id)
        ->where('node_type', NodeType::Formation)
        ->firstOrFail();

    $co = Appointment::where('unit_id', $formationUnit->id)
        ->where('abbreviation', 'CO')
        ->where('is_command', true)
        ->first();

    expect($co)->not()->toBeNull();
});

it('TTAG each flight has exactly 2 appointments', function () {
    Unit::where('formation_id', $this->ttag->id)
        ->where('node_type', NodeType::Flight)
        ->each(function (Unit $flight) {
            expect(Appointment::where('unit_id', $flight->id)->count())
                ->toBe(2, "Flight {$flight->abbreviation} should have 2 appointments");
        });
});

it('TTAG each squadron has exactly 3 appointments', function () {
    Unit::where('formation_id', $this->ttag->id)
        ->where('node_type', NodeType::Squadron)
        ->each(function (Unit $squadron) {
            expect(Appointment::where('unit_id', $squadron->id)->count())
                ->toBe(3, "Squadron {$squadron->abbreviation} should have 3 appointments");
        });
});

it('TTAG seeder is idempotent', function () {
    $count = Unit::where('formation_id', $this->ttag->id)->count();

    $this->seed(TtdfOrbatSeeder::class);

    expect(Unit::where('formation_id', $this->ttag->id)->count())->toBe($count);
});
