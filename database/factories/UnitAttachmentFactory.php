<?php

namespace MaxieWright\TtdfOrbat\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

class UnitAttachmentFactory extends Factory
{
    protected $model = UnitAttachment::class;

    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'attached_to_id' => Unit::factory(),
            'effective_from' => now(),
            'effective_to' => null,
            'authority' => null,
            'remarks' => null,
        ];
    }

    public function ended(): static
    {
        return $this->state([
            'effective_from' => now()->subMonths(6),
            'effective_to' => now(),
        ]);
    }

    public function withAuthority(string $authority): static
    {
        return $this->state(['authority' => $authority]);
    }
}
