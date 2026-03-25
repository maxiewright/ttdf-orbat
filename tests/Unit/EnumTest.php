<?php

use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Enums\VesselType;

it('NodeType cases are all PascalCase', function () {
    foreach (NodeType::cases() as $case) {
        expect(preg_match('/^[A-Z][a-zA-Z]+$/', $case->name))->toBe(1);
    }
});

it('NodeType label returns a non-empty string for every case', function () {
    foreach (NodeType::cases() as $case) {
        expect($case->label())->toBeString()->not()->toBeEmpty();
    }
});

it('NodeType toArray returns value=>label pairs', function () {
    $map = NodeType::toArray();

    expect(array_keys($map))->toEqual(array_map(fn (NodeType $case) => $case->value, NodeType::cases()));
    expect($map)->toHaveCount(count(NodeType::cases()));
});

it('NodeType isNaval returns true only for naval types', function () {
    expect(NodeType::Base->isNaval())->toBeTrue();
    expect(NodeType::Vessel->isNaval())->toBeTrue();
    expect(NodeType::Flotilla->isNaval())->toBeTrue();
    expect(NodeType::Station->isNaval())->toBeTrue();

    expect(NodeType::Battalion->isNaval())->toBeFalse();
    expect(NodeType::Platoon->isNaval())->toBeFalse();
    expect(NodeType::Flight->isNaval())->toBeFalse();
    expect(NodeType::Squadron->isNaval())->toBeFalse();
});

it('NodeType isArmy returns false for naval and air types', function () {
    collect([NodeType::Base, NodeType::Vessel, NodeType::Flotilla, NodeType::Station, NodeType::Squadron, NodeType::Flight, NodeType::Wing])
        ->each(fn (NodeType $node) => expect($node->isArmy())->toBeFalse());
});

it('RankCategory prefix returns the backing value', function () {
    expect(RankCategory::Commissioned->prefix())->toBe('OF');
    expect(RankCategory::OtherRanks->prefix())->toBe('OR');
});

it('UnitStatus cases exist for all lifecycle states', function () {
    $expected = [
        UnitStatus::Active,
        UnitStatus::Reserve,
        UnitStatus::Decommissioned,
        UnitStatus::Disbanded,
    ];

    collect($expected)->each(fn (UnitStatus $status) => expect(UnitStatus::cases())->toContain($status));
});

it('VesselType toArray contains all vessel classifications', function () {
    $cases = VesselType::cases();
    $array = VesselType::toArray();

    expect(count($array))->toBe(count($cases));
    expect(array_keys($array))->toEqual(array_map(fn (VesselType $case) => $case->value, $cases));
});

it('all enums have toArray method returning array', function () {
    $enums = [
        NodeType::class,
        ServiceBranch::class,
        UnitStatus::class,
        RankCategory::class,
        VesselType::class,
        FormationType::class,
    ];

    foreach ($enums as $enum) {
        $result = $enum::toArray();

        expect($result)->toBeArray();
        expect(count($result))->toBeGreaterThan(0);
    }
});
