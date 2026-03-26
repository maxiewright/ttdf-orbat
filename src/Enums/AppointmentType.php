<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum AppointmentType: string
{
    case Command = 'command';
    case Staff = 'staff';
    case Technical = 'technical';
    case Administrative = 'administrative';
    case Medical = 'medical';
    case Chaplain = 'chaplain';
    case Legal = 'legal';

    public function label(): string
    {
        return match ($this) {
            self::Command => 'Command',
            self::Staff => 'Staff',
            self::Technical => 'Technical',
            self::Administrative => 'Administrative',
            self::Medical => 'Medical',
            self::Chaplain => 'Chaplain',
            self::Legal => 'Legal',
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
