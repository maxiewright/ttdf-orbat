<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Traits\BelongsToOrbat;

// Anonymous consumer model that uses the trait
class TestPersonnel extends Model
{
    use BelongsToOrbat;

    protected $table = 'test_personnel';

    protected $guarded = [];
}

beforeEach(function () {
    Schema::create('test_personnel', function (Blueprint $table) {
        $table->id();
        $table->foreignId('formation_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('rank_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('test_personnel');
});

it('rank() relationship resolves correctly', function () {
    $rank = Rank::factory()->create();
    $person = TestPersonnel::create(['rank_id' => $rank->id]);

    expect($person->rank->id)->toBe($rank->id);
});

it('unit() relationship resolves correctly', function () {
    $unit = Unit::factory()->create();
    $person = TestPersonnel::create(['unit_id' => $unit->id]);

    expect($person->unit->id)->toBe($unit->id);
});

it('formation() relationship resolves correctly', function () {
    $formation = Formation::factory()->create();
    $person = TestPersonnel::create(['formation_id' => $formation->id]);

    expect($person->formation->id)->toBe($formation->id);
});

it('appointment() relationship resolves correctly', function () {
    $appointment = Appointment::factory()->create();
    $person = TestPersonnel::create(['appointment_id' => $appointment->id]);

    expect($person->appointment->id)->toBe($appointment->id);
});

it('all FK columns accept null', function () {
    $person = TestPersonnel::create([]);

    expect($person->rank)->toBeNull();
    expect($person->unit)->toBeNull();
    expect($person->formation)->toBeNull();
    expect($person->appointment)->toBeNull();
});

it('scopeInUnit filters by unit', function () {
    $unit = Unit::factory()->create();
    $other = Unit::factory()->create();

    TestPersonnel::create(['unit_id' => $unit->id]);
    TestPersonnel::create(['unit_id' => $other->id]);
    TestPersonnel::create(['unit_id' => null]);

    expect(TestPersonnel::inUnit($unit)->count())->toBe(1);
    expect(TestPersonnel::inUnit($unit->id)->count())->toBe(1);
});

it('scopeInFormation filters by formation', function () {
    $formation = Formation::factory()->create();
    $other = Formation::factory()->create();

    TestPersonnel::create(['formation_id' => $formation->id]);
    TestPersonnel::create(['formation_id' => $other->id]);

    expect(TestPersonnel::inFormation($formation)->count())->toBe(1);
    expect(TestPersonnel::inFormation($formation->id)->count())->toBe(1);
});

it('scopeWithRank filters by rank', function () {
    $rank = Rank::factory()->create();
    $other = Rank::factory()->create();

    TestPersonnel::create(['rank_id' => $rank->id]);
    TestPersonnel::create(['rank_id' => $other->id]);

    expect(TestPersonnel::withRank($rank)->count())->toBe(1);
    expect(TestPersonnel::withRank($rank->id)->count())->toBe(1);
});

it('scopeWithAppointment filters by appointment', function () {
    $appointment = Appointment::factory()->create();
    $other = Appointment::factory()->create();

    TestPersonnel::create(['appointment_id' => $appointment->id]);
    TestPersonnel::create(['appointment_id' => $other->id]);

    expect(TestPersonnel::withAppointment($appointment)->count())->toBe(1);
    expect(TestPersonnel::withAppointment($appointment->id)->count())->toBe(1);
});
