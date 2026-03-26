<?php

use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

beforeEach(function () {
    $this->seed(TtdfOrbatSeeder::class);
    $this->ttcg = Formation::where('abbreviation', 'TTCG')->firstOrFail();
});

it('TTCG has one top-level unit', function () {
    $topLevel = Unit::forFormation($this->ttcg)->topLevel()->get();

    expect($topLevel)->toHaveCount(1);
    expect($topLevel->first()->abbreviation)->toBe('TTCG');
});

it('TTCG top-level unit has expected direct children', function () {
    $root = Unit::where('designation', 'TTCG')->firstOrFail();
    $children = $root->children;

    // HQ, Base, Flotilla 1, Station Tobago
    expect($children)->toHaveCount(4);
});

it('TTCG has a flotilla with 6 vessels', function () {
    $flotilla = Unit::where('designation', 'FLOT1')->firstOrFail();

    expect($flotilla->node_type)->toBe(NodeType::Flotilla);
    expect($flotilla->children)->toHaveCount(6);
    expect($flotilla->children->every(fn (Unit $u) => $u->node_type === NodeType::Vessel))->toBeTrue();
});

it('TTCG Coast Guard Base has 3 departments', function () {
    $base = Unit::where('designation', 'CGB')->firstOrFail();

    expect($base->node_type)->toBe(NodeType::Base);
    expect($base->children)->toHaveCount(3);
    expect($base->children->every(fn (Unit $u) => $u->node_type === NodeType::Department))->toBeTrue();
});

it('TTCG HQ has 4 branch children', function () {
    $hq = Unit::where('designation', 'CGTHQ')->firstOrFail();

    expect($hq->node_type)->toBe(NodeType::Headquarters);
    expect($hq->children)->toHaveCount(4);
    expect($hq->children->every(fn (Unit $u) => $u->node_type === NodeType::Branch))->toBeTrue();
});

it('TTCG Station Tobago exists as a Station node', function () {
    $station = Unit::where('designation', 'CGST')->firstOrFail();

    expect($station->node_type)->toBe(NodeType::Station);
    expect($station->formation_id)->toBe($this->ttcg->id);
});

it('TTCG all vessels have Navy service branch', function () {
    $vessels = Unit::where('formation_id', $this->ttcg->id)
        ->where('node_type', NodeType::Vessel)
        ->get();

    expect($vessels)->not()->toBeEmpty();
    expect($vessels->every(fn (Unit $u) => $u->service_branch->value === 'navy'))->toBeTrue();
});

it('TTCG formation node has a Commandant appointment', function () {
    $formationUnit = Unit::where('formation_id', $this->ttcg->id)
        ->where('node_type', NodeType::Formation)
        ->firstOrFail();

    $co = Appointment::where('unit_id', $formationUnit->id)
        ->where('abbreviation', 'Comdt')
        ->first();

    expect($co)->not()->toBeNull();
    expect($co->is_command)->toBeTrue();
});

it('TTCG each vessel has 4 appointments', function () {
    Unit::where('formation_id', $this->ttcg->id)
        ->where('node_type', NodeType::Vessel)
        ->each(function (Unit $vessel) {
            expect(Appointment::where('unit_id', $vessel->id)->count())
                ->toBe(4, "Vessel {$vessel->abbreviation} should have 4 appointments");
        });
});

it('TTCG seeder is idempotent', function () {
    $count = Unit::where('formation_id', $this->ttcg->id)->count();

    $this->seed(TtdfOrbatSeeder::class);

    expect(Unit::where('formation_id', $this->ttcg->id)->count())->toBe($count);
});
