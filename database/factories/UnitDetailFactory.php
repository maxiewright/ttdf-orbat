<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Enums\VesselType;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitDetail;

class UnitDetailFactory extends Factory
{
    protected $model = UnitDetail::class;

    public function definition(): array
    {
        return [
            'detailable_type' => Unit::class,
            'detailable_id' => Unit::factory(),
        ];
    }

    public function forVessel(?Unit $unit = null): static
    {
        return $this->state(fn () => [
            'detailable_type' => Unit::class,
            'detailable_id' => $unit->id ?? Unit::factory()->vessel(),
            'hull_number' => (string) $this->faker->numberBetween(1, 99),
            'pennant_prefix' => $this->faker->randomElement(['CG', 'TTS']),
            'vessel_type' => $this->faker->randomElement(VesselType::cases()),
            'vessel_class' => $this->faker->words(2, true),
            'displacement_tons' => $this->faker->numberBetween(20, 2000),
            'crew_complement' => $this->faker->numberBetween(5, 80),
            'commissioned_at' => $this->faker->date(),
        ]);
    }

    public function forBase(?Unit $unit = null): static
    {
        return $this->state(fn () => [
            'detailable_type' => Unit::class,
            'detailable_id' => $unit->id ?? Unit::factory(),
            'base_location' => $this->faker->city(),
            'base_type' => $this->faker->randomElement(['main', 'forward', 'satellite']),
            'latitude' => $this->faker->latitude(10.0, 10.7),
            'longitude' => $this->faker->longitude(-61.9, -60.9),
        ]);
    }
}
