<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

abstract class AbstractUnitSeeder extends Seeder
{
    protected Formation $formation;

    abstract protected function defaultServiceBranch(): ServiceBranch;

    protected function make(string $code, array $data): Unit
    {
        $attributes = array_merge([
            'formation_id' => $this->formation->id,
            'service_branch' => $this->defaultServiceBranch(),
            'status' => UnitStatus::Active,
            'designation' => $code,
        ], $data);

        return Unit::updateOrCreate(
            [
                'formation_id' => $attributes['formation_id'],
                'designation' => $code,
            ],
            $attributes,
        );
    }
}
