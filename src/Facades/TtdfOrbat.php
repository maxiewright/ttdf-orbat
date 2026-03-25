<?php

namespace MaxieWright\TtdfOrbat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MaxieWright\TtdfOrbat\TtdfOrbat
 */
class TtdfOrbat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MaxieWright\TtdfOrbat\TtdfOrbat::class;
    }
}
