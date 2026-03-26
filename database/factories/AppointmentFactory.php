<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Unit;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'min_rank_grade_id' => null,
            'max_rank_grade_id' => null,
            'title' => $this->faker->unique()->words(2, true),
            'abbreviation' => $this->faker->unique()->lexify('???'),
            'category' => AppointmentCategory::Commissioned,
            'type' => AppointmentType::Command,
            'is_command' => false,
            'is_active' => true,
            'sort_order' => 0,
            'remarks' => null,
        ];
    }

    public function command(): static
    {
        return $this->state(['is_command' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
