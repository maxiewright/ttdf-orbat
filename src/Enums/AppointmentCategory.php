<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum AppointmentCategory: string
{
    case Commissioned = 'commissioned';
    case WarrantOfficer = 'warrant_officer';
    case OtherRanks = 'other_ranks';
    case Civilian = 'civilian';

    public function label(): string
    {
        return match ($this) {
            self::Commissioned => 'Commissioned Officer',
            self::WarrantOfficer => 'Warrant Officer',
            self::OtherRanks => 'Other Ranks',
            self::Civilian => 'Civilian',
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
