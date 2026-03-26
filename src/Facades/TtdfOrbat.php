<?php

namespace MaxieWright\TtdfOrbat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string version()
 * @method static \Illuminate\Database\Eloquent\Collection formations()
 * @method static \MaxieWright\TtdfOrbat\Models\Formation|null formation(string $abbreviation)
 * @method static \Illuminate\Database\Eloquent\Collection ranks(string $formationAbbreviation)
 * @method static \Illuminate\Database\Eloquent\Collection grades()
 * @method static \Illuminate\Database\Eloquent\Collection officers(string $formationAbbreviation)
 * @method static \Illuminate\Database\Eloquent\Collection otherRanks(string $formationAbbreviation)
 * @method static \Illuminate\Database\Eloquent\Collection tree(string $formationAbbreviation)
 * @method static \Illuminate\Database\Eloquent\Collection units(string $formationAbbreviation)
 * @method static \Illuminate\Database\Eloquent\Collection appointments(string|\MaxieWright\TtdfOrbat\Models\Unit $unit)
 * @method static \Illuminate\Database\Eloquent\Collection commandAppointments(string|\MaxieWright\TtdfOrbat\Models\Unit $unit)
 *
 * @see \MaxieWright\TtdfOrbat\TtdfOrbat
 */
class TtdfOrbat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MaxieWright\TtdfOrbat\TtdfOrbat::class;
    }
}
