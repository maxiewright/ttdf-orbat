<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum FormationType: string
{
    case Regiment = 'regiment';
    case CoastGuard = 'coast_guard';
    case AirGuard = 'air_guard';
    case Reserve = 'reserve';
    case Joint = 'joint';

    public function label(): string
    {
        return match ($this) {
            self::Regiment => 'Regiment',
            self::CoastGuard => 'Coast Guard',
            self::AirGuard => 'Air Guard',
            self::Reserve => 'Reserve',
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
