<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;

class RankFactory extends Factory
{
    protected $model = Rank::class;

    public function definition(): array
    {
        return [
            'rank_grade_id' => RankGrade::factory(),
            'formation_id' => Formation::factory(),
            'title' => $this->faker->words(2, true),
            'abbreviation' => $this->faker->lexify('???'),
            'insignia_path' => null,
        ];
    }
}
