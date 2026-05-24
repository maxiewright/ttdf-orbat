<?php

use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

beforeEach(function () {
    $this->seed(TtdfOrbatSeeder::class);
    $this->ttr = Formation::where('abbreviation', 'TTR')->firstOrFail();
});

it('seeds ALC as an active Detachment under SSB', function () {
    $alc = Unit::where('formation_id', $this->ttr->id)
        ->where('designation', 'ALC')
        ->firstOrFail();

    expect($alc->abbreviation)->toBe('ALC');
    expect($alc->name)->toBe('Army Learning Centre');
    expect($alc->node_type)->toBe(NodeType::Detachment);
    expect($alc->status)->toBe(UnitStatus::Active);
    expect($alc->parent->abbreviation)->toBe('SSB');
});

it('orders ALC after the existing SSB sub-units', function () {
    $mnCoy = Unit::where('formation_id', $this->ttr->id)->where('designation', 'MNCOYSSB')->firstOrFail();
    $alc = Unit::where('formation_id', $this->ttr->id)->where('designation', 'ALC')->firstOrFail();

    expect($alc->sort_order)->toBeGreaterThan($mnCoy->sort_order);
});

it('seeds exactly 47 TTR units', function () {
    expect(Unit::where('formation_id', $this->ttr->id)->count())->toBe(47);
});

it('retains the Sp Wpns detachment for abbreviation stability', function () {
    $spWpns = Unit::where('formation_id', $this->ttr->id)->where('abbreviation', 'Sp Wpns')->first();

    expect($spWpns)->not->toBeNull();
    expect($spWpns->node_type)->toBe(NodeType::Detachment);
});

it('gives every TTR detachment exactly 4 appointments', function () {
    $detachments = Unit::where('formation_id', $this->ttr->id)
        ->where('node_type', NodeType::Detachment)
        ->get();

    expect($detachments)->toHaveCount(3); // SFOD, Sp Wpns, ALC

    foreach ($detachments as $detachment) {
        expect(Appointment::where('unit_id', $detachment->id)->count())
            ->toBe(4, "Detachment {$detachment->abbreviation} should have 4 appointments");
    }
});

it('gives every TTR squadron exactly 4 appointments', function () {
    $squadrons = Unit::where('formation_id', $this->ttr->id)
        ->where('node_type', NodeType::Squadron)
        ->get();

    expect($squadrons)->toHaveCount(3); // Spt Sqn, Fld Con Sqn, EME Sqn

    foreach ($squadrons as $squadron) {
        expect(Appointment::where('unit_id', $squadron->id)->count())
            ->toBe(4, "Squadron {$squadron->abbreviation} should have 4 appointments");
    }
});

it('gives the RHQ headquarters exactly 4 appointments', function () {
    $hq = Unit::where('formation_id', $this->ttr->id)
        ->where('node_type', NodeType::Headquarters)
        ->firstOrFail();

    expect(Appointment::where('unit_id', $hq->id)->count())->toBe(4);
});

it('makes ALC Det Comd a command appointment spanning OF-3 to OF-4', function () {
    $alc = Unit::where('formation_id', $this->ttr->id)->where('designation', 'ALC')->firstOrFail();

    $detComd = Appointment::with(['minRankGrade', 'maxRankGrade'])
        ->where('unit_id', $alc->id)
        ->where('abbreviation', 'Det Comd')
        ->firstOrFail();

    expect($detComd->is_command)->toBeTrue();
    expect($detComd->minRankGrade->code)->toBe('OF-3');
    expect($detComd->maxRankGrade->code)->toBe('OF-4');
});

it('resolves ALC DSM to warrant grade WO-1', function () {
    $alc = Unit::where('formation_id', $this->ttr->id)->where('designation', 'ALC')->firstOrFail();

    $dsm = Appointment::with('minRankGrade')
        ->where('unit_id', $alc->id)
        ->where('abbreviation', 'DSM')
        ->firstOrFail();

    expect($dsm->minRankGrade->code)->toBe('WO-1');
});
