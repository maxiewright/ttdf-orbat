<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Models\RankGrade;

class RankGradeFactory extends Factory
{
    protected $model = RankGrade::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('??-#'),
            'nato_code' => null,
            'category' => $this->faker->randomElement(RankCategory::cases()),
            'level' => $this->faker->numberBetween(1, 9),
            'label' => $this->faker->words(2, true),
        ];
    }

    public function officer(): static
    {
        return $this->state(['category' => RankCategory::Commissioned]);
    }

    public function warrant(): static
    {
        return $this->state(['category' => RankCategory::Warrant]);
    }

    public function otherRanks(): static
    {
        return $this->state(['category' => RankCategory::OtherRanks]);
    }
}
