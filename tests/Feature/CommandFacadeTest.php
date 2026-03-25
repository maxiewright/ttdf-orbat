<?php

use MaxieWright\TtdfOrbat\Facades\TtdfOrbat as TtdfOrbatFacade;
use MaxieWright\TtdfOrbat\TtdfOrbat;

it('ttdf-orbat:stats command runs successfully', function () {
    $this->artisan('ttdf-orbat:stats')
        ->assertSuccessful();
});

it('ttdf-orbat:validate command runs successfully on empty database', function () {
    $this->artisan('ttdf-orbat:validate')
        ->assertSuccessful();
});

it('facade resolves the package class', function () {
    expect(TtdfOrbatFacade::getFacadeRoot())->toBeInstanceOf(TtdfOrbat::class);
});
