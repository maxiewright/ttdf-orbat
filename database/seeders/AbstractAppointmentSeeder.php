<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

abstract class AbstractAppointmentSeeder extends Seeder
{
    /** @var array<string, int> */
    private array $gradeIds = [];

    abstract protected function formationAbbreviation(): string;

    /**
     * @return array<string, list<array{title: string, abbreviation: string, category: AppointmentCategory, type: AppointmentType, is_command: bool, min_grade: string|null, max_grade: string|null}>>
     */
    abstract protected function templates(): array;

    public function run(): void
    {
        $this->gradeIds = RankGrade::pluck('id', 'code')->all();

        $formation = Formation::where('abbreviation', $this->formationAbbreviation())->firstOrFail();

        $templates = $this->templates();
        $nodeTypes = array_keys($templates);

        Unit::where('formation_id', $formation->id)
            ->whereIn('node_type', $nodeTypes)
            ->each(function (Unit $unit) use ($templates): void {
                $template = $templates[$unit->node_type->value] ?? null;

                if ($template === null) {
                    return;
                }

                foreach ($template as $sortOrder => $row) {
                    $attributes = [
                        'unit_id' => $unit->id,
                        'abbreviation' => $row['abbreviation'],
                    ];

                    $values = [
                        'title' => $row['title'],
                        'category' => $row['category'],
                        'type' => $row['type'],
                        'is_command' => $row['is_command'],
                        'min_rank_grade_id' => $this->gradeId($row['min_grade']),
                        'max_rank_grade_id' => $this->gradeId($row['max_grade']),
                        'is_active' => true,
                        'sort_order' => $sortOrder,
                    ];

                    $appointment = Appointment::withTrashed()->firstOrNew($attributes);

                    foreach ($values as $key => $value) {
                        $appointment->{$key} = $value;
                    }

                    $appointment->deleted_at = null;
                    $appointment->save();
                }
            });
    }

    private function gradeId(?string $code): ?int
    {
        if ($code === null) {
            return null;
        }

        return $this->gradeIds[$code] ?? throw new \RuntimeException("Unknown rank grade code: {$code}");
    }
}
