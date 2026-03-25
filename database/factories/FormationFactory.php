<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Enums\FormationType;
use MaxieWright\TtdfOrbat\Models\Formation;

class FormationFactory extends Factory
{
    protected $model = Formation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'abbreviation' => $this->faker->unique()->lexify('???'),
            'type' => $this->faker->randomElement(FormationType::cases()),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function regiment(): static
    {
        return $this->state(['type' => FormationType::Regiment]);
    }

    public function coastGuard(): static
    {
        return $this->state(['type' => FormationType::CoastGuard]);
    }

    public function airGuard(): static
    {
        return $this->state(['type' => FormationType::AirGuard]);
    }
}
