<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum VesselType: string
{
    case OffshorePatrol = 'opv';
    case CoastalPatrol = 'cpv';
    case FastPatrol = 'fpc';
    case FastCrewSupply = 'fcs';
    case Interceptor = 'interceptor';

    public function label(): string
    {
        return match ($this) {
            self::OffshorePatrol => 'Offshore Patrol Vessel',
            self::CoastalPatrol => 'Coastal Patrol Vessel',
            self::FastPatrol => 'Fast Patrol Craft',
            self::FastCrewSupply => 'Fast Crew Supply',
            self::Interceptor => 'Interceptor',
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
