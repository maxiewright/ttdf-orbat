<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum ServiceBranch: string
{
    case Army = 'army';
    case Navy = 'navy';
    case Air = 'air';
    case Joint = 'joint';

    public function label(): string
    {
        return match ($this) {
            self::Army => 'Army',
            self::Navy => 'Navy',
            self::Air => 'Air',
            self::Joint => 'Joint',
        };
    }

    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->label(), self::cases()),
        );
    }
}
