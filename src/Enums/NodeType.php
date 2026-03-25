<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum NodeType: string
{
    case Force = 'force';
    case Formation = 'formation';
    case Headquarters = 'headquarters';
    case Directorate = 'directorate';
    case Department = 'department';
    case Branch = 'branch';
    case Battalion = 'battalion';
    case Company = 'company';
    case Platoon = 'platoon';
    case Section = 'section';
    case Base = 'base';
    case Squadron = 'squadron';
    case Vessel = 'vessel';
    case Flight = 'flight';
    case Detachment = 'detachment';
    case Installation = 'installation';
    case Unit = 'unit';

    public function label(): string
    {
        return match ($this) {
            self::Force => 'Force',
            self::Formation => 'Formation',
            self::Headquarters => 'Headquarters',
            self::Directorate => 'Directorate',
            self::Department => 'Department',
            self::Branch => 'Branch',
            self::Battalion => 'Battalion',
            self::Company => 'Company',
            self::Platoon => 'Platoon',
            self::Section => 'Section',
            self::Base => 'Base',
            self::Squadron => 'Squadron',
            self::Vessel => 'Vessel',
            self::Flight => 'Flight',
            self::Detachment => 'Detachment',
            self::Installation => 'Installation',
            self::Unit => 'Unit',
        };
    }

    public function isNaval(): bool
    {
        return in_array($this, [self::Base, self::Vessel, self::Squadron]);
    }

    public function isAir(): bool
    {
        return in_array($this, [self::Squadron, self::Flight]);
    }

    public function isArmy(): bool
    {
        return in_array($this, [self::Battalion, self::Company, self::Platoon, self::Section]);
    }

    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->label(), self::cases()),
        );
    }
}
