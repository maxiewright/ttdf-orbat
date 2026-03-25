<?php

namespace MaxieWright\TtdfOrbat\Enums;

enum RankCategory: string
{
    case Commissioned = 'OF';
    case Warrant = 'WO';
    case OtherRanks = 'OR';

    public function label(): string
    {
        return match ($this) {
            self::Commissioned => 'Commissioned',
            self::Warrant => 'Warrant',
            self::OtherRanks => 'Other Ranks',
        };
    }

    public function prefix(): string
    {
        return $this->value;
    }

    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->label(), self::cases()),
        );
    }
}
