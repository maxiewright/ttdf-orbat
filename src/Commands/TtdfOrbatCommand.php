<?php

namespace MaxieWright\TtdfOrbat\Commands;

use Illuminate\Console\Command;

class TtdfOrbatCommand extends Command
{
    public $signature = 'ttdf-orbat';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
