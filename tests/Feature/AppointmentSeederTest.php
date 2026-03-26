<?php

use Illuminate\Database\Eloquent\Collection;
use MaxieWright\TtdfOrbat\Database\Seeders\TtdfOrbatSeeder;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\TtdfOrbat;

beforeEach(function () {
    $this->seed(TtdfOrbatSeeder::class);
    $this->ttr = Formation::where('abbreviation', 'TTR')->firstOrFail();
});

function ttrUnit(object $context, NodeType $type): Unit
{
    return Unit::where('formation_id', $context->ttr->id)
        ->where('node_type', $type)
        ->firstOrFail();
}

function ttrUnits(object $context, NodeType $type): Collection
{
    return Unit::where('formation_id', $context->ttr->id)
        ->where('node_type', $type)
        ->get();
}

it('formation node has exactly 16 appointments', function () {
    $formationUnit = ttrUnit($this, NodeType::Formation);

    expect(Appointment::where('unit_id', $formationUnit->id)->count())->toBe(16);
});

it('each TTR battalion has exactly 6 appointments', function () {
    foreach (ttrUnits($this, NodeType::Battalion) as $battalion) {
        expect(Appointment::where('unit_id', $battalion->id)->count())
            ->toBe(6, "Battalion {$battalion->abbreviation} should have 6 appointments");
    }
});

it('each TTR company has exactly 4 appointments', function () {
    foreach (ttrUnits($this, NodeType::Company) as $company) {
        expect(Appointment::where('unit_id', $company->id)->count())
            ->toBe(4, "Company {$company->abbreviation} should have 4 appointments");
    }
});

it('each TTR platoon has exactly 2 appointments', function () {
    foreach (ttrUnits($this, NodeType::Platoon) as $platoon) {
        expect(Appointment::where('unit_id', $platoon->id)->count())
            ->toBe(2, "Platoon {$platoon->abbreviation} should have 2 appointments");
    }
});

it('formation node has exactly one is_command appointment with abbreviation CO', function () {
    $formationUnit = ttrUnit($this, NodeType::Formation);

    $commandAppts = Appointment::where('unit_id', $formationUnit->id)
        ->where('is_command', true)
        ->get();

    expect($commandAppts)->toHaveCount(1);
    expect($commandAppts->first()->abbreviation)->toBe('CO');
});

it('formation node has all G-staff appointments', function () {
    $formationUnit = ttrUnit($this, NodeType::Formation);

    $gStaff = Appointment::where('unit_id', $formationUnit->id)
        ->whereIn('abbreviation', ['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G8', 'G9'])
        ->pluck('abbreviation')
        ->sort()
        ->values();

    expect($gStaff->all())->toBe(['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G8', 'G9']);
});

it('battalion CO appointment has is_command true', function () {
    $battalion = ttrUnit($this, NodeType::Battalion);

    $co = Appointment::where('unit_id', $battalion->id)
        ->where('abbreviation', 'CO')
        ->firstOrFail();

    expect($co->is_command)->toBeTrue();
});

it('company OC appointment has is_command true', function () {
    $company = ttrUnit($this, NodeType::Company);

    $oc = Appointment::where('unit_id', $company->id)
        ->where('abbreviation', 'OC')
        ->firstOrFail();

    expect($oc->is_command)->toBeTrue();
});

it('platoon Pl Comd appointment has is_command true', function () {
    $platoon = ttrUnit($this, NodeType::Platoon);

    $plComd = Appointment::where('unit_id', $platoon->id)
        ->where('abbreviation', 'Pl Comd')
        ->firstOrFail();

    expect($plComd->is_command)->toBeTrue();
});

it('battalion CO min_rank_grade resolves to grade code OF-5', function () {
    $battalion = ttrUnit($this, NodeType::Battalion);

    $co = Appointment::with('minRankGrade')
        ->where('unit_id', $battalion->id)
        ->where('abbreviation', 'CO')
        ->firstOrFail();

    expect($co->minRankGrade->code)->toBe('OF-5');
});

it('company OC min/max span OF-3 to OF-4', function () {
    $company = ttrUnit($this, NodeType::Company);

    $oc = Appointment::with(['minRankGrade', 'maxRankGrade'])
        ->where('unit_id', $company->id)
        ->where('abbreviation', 'OC')
        ->firstOrFail();

    expect($oc->minRankGrade->code)->toBe('OF-3');
    expect($oc->maxRankGrade->code)->toBe('OF-4');
});

it('seeder is idempotent — running twice does not change the appointment count', function () {
    $count = Appointment::count();

    $this->seed(TtdfOrbatSeeder::class);

    expect(Appointment::count())->toBe($count);
});

it('facade appointments() returns active appointments for a unit', function () {
    $unit = ttrUnit($this, NodeType::Battalion);
    $facade = app(TtdfOrbat::class);

    $appointments = $facade->appointments($unit);

    expect($appointments)->toHaveCount(6);
    expect($appointments->first())->toBeInstanceOf(Appointment::class);
    expect($appointments->first()->relationLoaded('minRankGrade'))->toBeTrue();
});

it('facade appointments() accepts abbreviation string', function () {
    $facade = app(TtdfOrbat::class);

    $appointments = $facade->appointments('1TTR');

    expect($appointments)->toHaveCount(6);
});

it('facade appointments() returns empty collection for unknown abbreviation', function () {
    $facade = app(TtdfOrbat::class);

    $appointments = $facade->appointments('NONEXISTENT');

    expect($appointments)->toBeEmpty();
});

it('facade commandAppointments() returns only command appointments', function () {
    $unit = ttrUnit($this, NodeType::Battalion);
    $facade = app(TtdfOrbat::class);

    $command = $facade->commandAppointments($unit);

    expect($command)->toHaveCount(1);
    expect($command->first()->abbreviation)->toBe('CO');
    expect($command->first()->is_command)->toBeTrue();
});
