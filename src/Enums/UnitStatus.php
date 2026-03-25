<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum UnitStatus: string
{
    case Active = 'active';
    case Reserve = 'reserve';
    case Decommissioned = 'decommissioned';
    case Disbanded = 'disbanded';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Reserve => 'Reserve',
            self::Decommissioned => 'Decommissioned',
            self::Disbanded => 'Disbanded',
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
