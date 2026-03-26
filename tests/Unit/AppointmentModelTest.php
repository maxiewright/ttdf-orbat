<?php

use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

function createFormation(array $attributes = []): Formation
{
    return Formation::create(array_merge([
        'name' => 'Regiment',
        'abbreviation' => uniqid('FRM'),
        'type' => FormationType::Regiment,
        'is_active' => true,
    ], $attributes));
}

function createUnit(?Formation $formation = null, array $attributes = []): Unit
{
    $formation ??= createFormation();

    return Unit::create(array_merge([
        'formation_id' => $formation->id,
        'node_type' => NodeType::Company,
        'service_branch' => ServiceBranch::Army,
        'name' => 'Test Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ], $attributes));
}

function createGrade(string $code, array $attributes = []): RankGrade
{
    return RankGrade::create(array_merge([
        'code' => $code,
        'category' => 'OF',
        'level' => 1,
        'label' => $code,
    ], $attributes));
}

it('can create an appointment with required fields', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Commanding Officer',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'is_command' => true,
        'is_active' => true,
        'sort_order' => 0,
    ]);

    expect($appointment->exists)->toBeTrue();
    expect($appointment->title)->toBe('Commanding Officer');
});

it('casts category and type to their enum types', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Officer Commanding',
        'abbreviation' => 'OC',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
    ]);

    $fresh = $appointment->fresh();
    expect($fresh->category)->toBe(AppointmentCategory::Commissioned);
    expect($fresh->type)->toBe(AppointmentType::Command);
});

it('casts is_command and is_active as booleans', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'CO',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'is_command' => 1,
        'is_active' => 0,
    ]);

    $fresh = $appointment->fresh();
    expect($fresh->is_command)->toBeTrue()->and($fresh->is_command)->toBeBool();
    expect($fresh->is_active)->toBeFalse()->and($fresh->is_active)->toBeBool();
});

it('unit() relationship returns the correct unit', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'CO',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
    ]);

    expect($appointment->unit->id)->toBe($unit->id);
});

it('minRankGrade() and maxRankGrade() relationships resolve correctly', function () {
    $unit = createUnit();
    $minGrade = createGrade('OF-1');
    $maxGrade = createGrade('OF-3');

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Platoon Commander',
        'abbreviation' => 'Pl Comd',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'min_rank_grade_id' => $minGrade->id,
        'max_rank_grade_id' => $maxGrade->id,
    ]);

    expect($appointment->minRankGrade->id)->toBe($minGrade->id);
    expect($appointment->maxRankGrade->id)->toBe($maxGrade->id);
});

it('both rank grade FKs accept null', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Liaison Officer',
        'abbreviation' => 'LO',
        'category' => AppointmentCategory::Civilian,
        'type' => AppointmentType::Staff,
        'min_rank_grade_id' => null,
        'max_rank_grade_id' => null,
    ]);

    expect($appointment->min_rank_grade_id)->toBeNull();
    expect($appointment->max_rank_grade_id)->toBeNull();
    expect($appointment->minRankGrade)->toBeNull();
    expect($appointment->maxRankGrade)->toBeNull();
});

it('rank_range accessor returns "Min – Max" when grades differ', function () {
    $unit = createUnit();
    $minGrade = createGrade('OF-3');
    $maxGrade = createGrade('OF-4');

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'OC',
        'abbreviation' => 'OC',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'min_rank_grade_id' => $minGrade->id,
        'max_rank_grade_id' => $maxGrade->id,
    ]);

    $appointment->load(['minRankGrade', 'maxRankGrade']);
    expect($appointment->rank_range)->toBe('OF-3 – OF-4');
});

it('rank_range accessor returns single label when min equals max', function () {
    $unit = createUnit();
    $grade = createGrade('WO-2');

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'RSM',
        'abbreviation' => 'RSM',
        'category' => AppointmentCategory::WarrantOfficer,
        'type' => AppointmentType::Administrative,
        'min_rank_grade_id' => $grade->id,
        'max_rank_grade_id' => $grade->id,
    ]);

    $appointment->load(['minRankGrade', 'maxRankGrade']);
    expect($appointment->rank_range)->toBe('WO-2');
});

it('rank_range accessor returns dash when both grades are null', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Civilian Advisor',
        'abbreviation' => 'CA',
        'category' => AppointmentCategory::Civilian,
        'type' => AppointmentType::Staff,
    ]);

    $appointment->load(['minRankGrade', 'maxRankGrade']);
    expect($appointment->rank_range)->toBe('—');
});

it('rank_range accessor returns single code when only min is set', function () {
    $unit = createUnit();
    $grade = createGrade('OF-2');

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Liaison',
        'abbreviation' => 'LNO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Staff,
        'min_rank_grade_id' => $grade->id,
        'max_rank_grade_id' => null,
    ]);

    $appointment->load(['minRankGrade', 'maxRankGrade']);
    expect($appointment->rank_range)->toBe('OF-2');
});

it('rank_range accessor returns single code when only max is set', function () {
    $unit = createUnit();
    $grade = createGrade('OF-5');

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Advisor',
        'abbreviation' => 'ADV',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Staff,
        'min_rank_grade_id' => null,
        'max_rank_grade_id' => $grade->id,
    ]);

    $appointment->load(['minRankGrade', 'maxRankGrade']);
    expect($appointment->rank_range)->toBe('OF-5');
});

it('full_title accessor returns "ABBR (Title)" format', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Commanding Officer',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
    ]);

    expect($appointment->full_title)->toBe('CO (Commanding Officer)');
});

it('active scope excludes inactive appointments', function () {
    $unit = createUnit();

    Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Active Post',
        'abbreviation' => 'AP',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'is_active' => true,
    ]);

    Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Inactive Post',
        'abbreviation' => 'IP',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'is_active' => false,
    ]);

    $active = Appointment::active()->where('unit_id', $unit->id)->get();
    expect($active)->toHaveCount(1);
    expect($active->first()->abbreviation)->toBe('AP');
});

it('command scope returns only command appointments', function () {
    $unit = createUnit();

    Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'CO',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
        'is_command' => true,
    ]);

    Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'Adjutant',
        'abbreviation' => 'Adjt',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Staff,
        'is_command' => false,
    ]);

    $command = Appointment::command()->where('unit_id', $unit->id)->get();
    expect($command)->toHaveCount(1);
    expect($command->first()->abbreviation)->toBe('CO');
});

it('forUnit scope filters correctly by unit', function () {
    $unit1 = createUnit();
    $unit2 = createUnit();

    Appointment::create([
        'unit_id' => $unit1->id,
        'title' => 'CO',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
    ]);

    Appointment::create([
        'unit_id' => $unit2->id,
        'title' => 'OC',
        'abbreviation' => 'OC',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
    ]);

    expect(Appointment::forUnit($unit1)->get())->toHaveCount(1);
    expect(Appointment::forUnit($unit1->id)->get())->toHaveCount(1);
    expect(Appointment::forUnit($unit1)->first()->abbreviation)->toBe('CO');
});

it('soft deletes work correctly', function () {
    $unit = createUnit();

    $appointment = Appointment::create([
        'unit_id' => $unit->id,
        'title' => 'CO',
        'abbreviation' => 'CO',
        'category' => AppointmentCategory::Commissioned,
        'type' => AppointmentType::Command,
    ]);

    $appointment->delete();

    expect(Appointment::where('unit_id', $unit->id)->count())->toBe(0);
    expect(Appointment::withTrashed()->where('unit_id', $unit->id)->count())->toBe(1);
});
