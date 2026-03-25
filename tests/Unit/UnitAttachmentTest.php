<?php

use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

function makeAttachmentUnit(string $abbreviation = 'TTR'): Unit
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
        'name' => 'Unit',
        'status' => UnitStatus::Active,
        'sort_order' => 0,
    ]);
}

it('detach sets effective_to to today', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('TTCG');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now()->subDays(10),
    ]);

    $attachment->detach();

    expect($attachment->effective_to->isToday())->toBeTrue();
});

it('detach returns true on success', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('TTCG-2');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now(),
    ]);

    expect($attachment->detach())->toBeTrue();
});

it('detach appends authority to remarks', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('TTCG-9');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now()->subDays(5),
    ]);

    $attachment->detach('Directive 1');

    expect($attachment->remarks)->toContain('Detached: Directive 1');
});

it('isActive returns false after detach is called', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('TTCG-3');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now(),
    ]);

    $attachment->detach();

    expect($attachment->isActive)->toBeFalse();
});

it('duration returns null when still attached', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('TTCG-4');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now()->subDays(30),
    ]);

    expect($attachment->duration)->toBeNull();
});

it('duration returns human readable string when detached', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('TTCG-5');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now()->subDays(90),
        'effective_to' => now(),
    ]);

    expect($attachment->duration)->toBeString()->not()->toBeEmpty();
});

it('current scope returns only active attachments', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('ActiveTarget');

    UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now()->subDays(1),
    ]);

    $other = makeAttachmentUnit('Inactive');

    UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $other->id,
        'effective_from' => now()->subDays(10),
        'effective_to' => now()->subDays(1),
    ]);

    expect(UnitAttachment::current()->count())->toBe(1);
});

it('historical scope returns only ended attachments', function () {
    $unit = makeAttachmentUnit();
    $target = makeAttachmentUnit('HistTarget');

    UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now()->subDays(10),
        'effective_to' => now()->subDays(1),
    ]);

    UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $target->id,
        'effective_from' => now(),
    ]);

    expect(UnitAttachment::historical()->count())->toBe(1);
});

it('forUnit scope filters by unit_id', function () {
    $unit = makeAttachmentUnit();

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => makeAttachmentUnit('FilterTarget')->id,
        'effective_from' => now(),
    ]);

    expect(UnitAttachment::forUnit($unit)->first()->id)->toBe($attachment->id);
});

it('atUnit scope filters by attached_to_id', function () {
    $unit = makeAttachmentUnit();
    $attached = makeAttachmentUnit('AttachedTo');

    $attachment = UnitAttachment::create([
        'unit_id' => $unit->id,
        'attached_to_id' => $attached->id,
        'effective_from' => now(),
    ]);

    expect(UnitAttachment::atUnit($attached)->first()->id)->toBe($attachment->id);
});
