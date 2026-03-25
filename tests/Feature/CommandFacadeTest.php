<?php

use Illuminate\Console\OutputStyle;
use MaxieWright\TtdfOrbat\Commands\TtdfOrbatCommand;
use MaxieWright\TtdfOrbat\Facades\TtdfOrbat as TtdfOrbatFacade;
use MaxieWright\TtdfOrbat\TtdfOrbat;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

it('runs the ttdf-orbat command', function () {
    $command = app(TtdfOrbatCommand::class);
    $command->setLaravel($this->app);
    $command->setOutput(new OutputStyle(new ArrayInput([]), new BufferedOutput()));

    expect($command->handle())->toBe(TtdfOrbatCommand::SUCCESS);
});

it('facade resolves the package class', function () {
    expect(TtdfOrbatFacade::getFacadeRoot())->toBeInstanceOf(TtdfOrbat::class);
});
