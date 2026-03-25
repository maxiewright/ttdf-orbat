<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'formation_id' => Formation::factory(),
            'parent_id' => null,
            'node_type' => NodeType::Company,
            'service_branch' => ServiceBranch::Army,
            'name' => $this->faker->unique()->words(2, true),
            'abbreviation' => $this->faker->unique()->lexify('???'),
            'designation' => null,
            'status' => UnitStatus::Active,
            'sort_order' => 0,
            'activated_at' => null,
            'deactivated_at' => null,
        ];
    }

    public function childOf(Unit $parent): static
    {
        return $this->state([
            'parent_id' => $parent->id,
            'formation_id' => $parent->formation_id,
            'service_branch' => $parent->service_branch,
        ]);
    }

    public function battalion(): static
    {
        return $this->state(['node_type' => NodeType::Battalion]);
    }

    public function company(): static
    {
        return $this->state(['node_type' => NodeType::Company]);
    }

    public function platoon(): static
    {
        return $this->state(['node_type' => NodeType::Platoon]);
    }

    public function headquarters(): static
    {
        return $this->state(['node_type' => NodeType::Headquarters]);
    }

    public function vessel(): static
    {
        return $this->state([
            'node_type' => NodeType::Vessel,
            'service_branch' => ServiceBranch::Navy,
        ]);
    }

    public function detachment(): static
    {
        return $this->state(['node_type' => NodeType::Detachment]);
    }

    public function decommissioned(): static
    {
        return $this->state([
            'status' => UnitStatus::Decommissioned,
            'deactivated_at' => now(),
        ]);
    }

    public function disbanded(): static
    {
        return $this->state([
            'status' => UnitStatus::Disbanded,
            'deactivated_at' => now(),
        ]);
    }
}
