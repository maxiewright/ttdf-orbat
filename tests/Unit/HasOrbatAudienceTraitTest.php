<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\OrbatAudience;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Traits\HasOrbatAudience;

// Anonymous consumer model that uses the trait
class TestNotice extends Model
{
    use HasOrbatAudience;

    protected $table = 'test_notices';

    protected $guarded = [];
}

beforeEach(function () {
    Schema::create('test_notices', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('test_notices');
});

it('orbatAudiences() returns a MorphMany relation', function () {
    $notice = TestNotice::create(['title' => 'Test']);

    expect($notice->orbatAudiences())->toBeInstanceOf(MorphMany::class);
});

it('addAudience() creates an OrbatAudience record for a Formation', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $formation = Formation::factory()->create();

    $notice->addAudience($formation);

    expect(OrbatAudience::count())->toBe(1);
    expect(OrbatAudience::first()->targetable_type)->toBe($formation->getMorphClass());
    expect(OrbatAudience::first()->targetable_id)->toBe($formation->id);
});

it('addAudience() creates an OrbatAudience record for a Unit', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $unit = Unit::factory()->create();

    $notice->addAudience($unit);

    expect($notice->orbatAudiences()->count())->toBe(1);
    expect($notice->orbatAudiences()->first()->targetable_id)->toBe($unit->id);
});

it('addAudience() creates an OrbatAudience record for a RankGrade', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $grade = RankGrade::factory()->create();

    $notice->addAudience($grade);

    expect($notice->orbatAudiences()->count())->toBe(1);
});

it('addAudience() is idempotent — duplicate call does not create second record', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $formation = Formation::factory()->create();

    $notice->addAudience($formation);
    $notice->addAudience($formation);

    expect(OrbatAudience::count())->toBe(1);
});

it('addAudience() can target multiple different entities', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $f1 = Formation::factory()->create();
    $f2 = Formation::factory()->create();

    $notice->addAudience($f1);
    $notice->addAudience($f2);

    expect($notice->orbatAudiences()->count())->toBe(2);
});

it('removeAudience() deletes the matching audience record', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $f1 = Formation::factory()->create();
    $f2 = Formation::factory()->create();

    $notice->addAudience($f1);
    $notice->addAudience($f2);
    $notice->removeAudience($f1);

    expect($notice->orbatAudiences()->count())->toBe(1);
    expect($notice->isTargetedAt($f1))->toBeFalse();
    expect($notice->isTargetedAt($f2))->toBeTrue();
});

it('isTargetedAt() returns true when audience exists', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $formation = Formation::factory()->create();

    $notice->addAudience($formation);

    expect($notice->isTargetedAt($formation))->toBeTrue();
});

it('isTargetedAt() returns false when audience does not exist', function () {
    $notice = TestNotice::create(['title' => 'Test']);
    $formation = Formation::factory()->create();

    expect($notice->isTargetedAt($formation))->toBeFalse();
});

it('scopeForAudience() returns only models targeted at a given entity', function () {
    $f1 = Formation::factory()->create();
    $f2 = Formation::factory()->create();

    $n1 = TestNotice::create(['title' => 'For F1']);
    $n2 = TestNotice::create(['title' => 'For F2']);
    $n3 = TestNotice::create(['title' => 'For both']);

    $n1->addAudience($f1);
    $n2->addAudience($f2);
    $n3->addAudience($f1);
    $n3->addAudience($f2);

    $results = TestNotice::forAudience($f1)->get();

    expect($results)->toHaveCount(2);
    expect($results->pluck('title')->sort()->values()->all())->toBe(['For F1', 'For both']);
});

it('different models targeting the same entity are independent', function () {
    $formation = Formation::factory()->create();

    $n1 = TestNotice::create(['title' => 'Notice 1']);
    $n2 = TestNotice::create(['title' => 'Notice 2']);

    $n1->addAudience($formation);

    expect($n1->isTargetedAt($formation))->toBeTrue();
    expect($n2->isTargetedAt($formation))->toBeFalse();
});
