<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use Illuminate\Database\Seeder;
use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

class TtrAppointmentSeeder extends Seeder
{
    /** @var array<string, int> */
    private array $gradeIds = [];

    public function run(): void
    {
        $this->gradeIds = RankGrade::pluck('id', 'code')->all();

        $formation = Formation::where('abbreviation', 'TTR')->firstOrFail();

        $templates = $this->templates();
        $nodeTypes = array_keys($templates);

        Unit::where('formation_id', $formation->id)
            ->whereIn('node_type', $nodeTypes)
            ->each(function (Unit $unit) use ($templates) {
                $template = $templates[$unit->node_type->value] ?? null;

                if ($template === null) {
                    return;
                }

                foreach ($template as $sortOrder => $row) {
                    Appointment::updateOrCreate(
                        [
                            'unit_id' => $unit->id,
                            'abbreviation' => $row['abbreviation'],
                        ],
                        [
                            'title' => $row['title'],
                            'category' => $row['category'],
                            'type' => $row['type'],
                            'is_command' => $row['is_command'],
                            'min_rank_grade_id' => $this->gradeId($row['min_grade']),
                            'max_rank_grade_id' => $this->gradeId($row['max_grade']),
                            'is_active' => true,
                            'sort_order' => $sortOrder,
                        ],
                    );
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

    /**
     * @return array<string, list<array{title: string, abbreviation: string, category: AppointmentCategory, type: AppointmentType, is_command: bool, min_grade: string|null, max_grade: string|null}>>
     */
    private function templates(): array
    {
        return [
            NodeType::Formation->value => [
                ['title' => 'Commanding Officer', 'abbreviation' => 'CO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-7', 'max_grade' => 'OF-8'],
                ['title' => 'Deputy Commanding Officer', 'abbreviation' => 'DCO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-6', 'max_grade' => 'OF-7'],
                ['title' => 'Chief of Staff', 'abbreviation' => 'COS', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-5', 'max_grade' => 'OF-6'],
                ['title' => 'Regimental Sergeant Major', 'abbreviation' => 'RSM', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-2', 'max_grade' => 'WO-2'],
            ],
            NodeType::Battalion->value => [
                ['title' => 'Commanding Officer', 'abbreviation' => 'CO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-5', 'max_grade' => 'OF-5'],
                ['title' => 'Second in Command', 'abbreviation' => '2IC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-4'],
                ['title' => 'Adjutant', 'abbreviation' => 'Adjt', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-3'],
                ['title' => 'Quartermaster', 'abbreviation' => 'QM', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Regimental Sergeant Major', 'abbreviation' => 'RSM', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'WO-2', 'max_grade' => 'WO-2'],
                ['title' => 'Regimental Quartermaster Sergeant', 'abbreviation' => 'RQMS', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-1', 'max_grade' => 'WO-1'],
            ],
            NodeType::Company->value => [
                ['title' => 'Officer Commanding', 'abbreviation' => 'OC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Second in Command', 'abbreviation' => '2IC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-2', 'max_grade' => 'OF-3'],
                ['title' => 'Company Sergeant Major', 'abbreviation' => 'CSM', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'WO-1', 'max_grade' => 'WO-1'],
                ['title' => 'Company Quartermaster Sergeant', 'abbreviation' => 'CQMS', 'category' => AppointmentCategory::OtherRanks, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'OR-5', 'max_grade' => 'OR-5'],
            ],
            NodeType::Platoon->value => [
                ['title' => 'Platoon Commander', 'abbreviation' => 'Pl Comd', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-1', 'max_grade' => 'OF-2'],
                ['title' => 'Platoon Sergeant', 'abbreviation' => 'Pl Sgt', 'category' => AppointmentCategory::OtherRanks, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OR-4', 'max_grade' => 'OR-4'],
            ],
        ];
    }
}
