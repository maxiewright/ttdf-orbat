<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Enums\NodeType;

class TtcgAppointmentSeeder extends AbstractAppointmentSeeder
{
    protected function formationAbbreviation(): string
    {
        return 'TTCG';
    }

    /**
     * @return array<string, list<array{title: string, abbreviation: string, category: AppointmentCategory, type: AppointmentType, is_command: bool, min_grade: string|null, max_grade: string|null}>>
     */
    protected function templates(): array
    {
        return [
            NodeType::Formation->value => [
                ['title' => 'Commandant', 'abbreviation' => 'Comdt', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-7', 'max_grade' => 'OF-8'],
                ['title' => 'Deputy Commandant', 'abbreviation' => 'Dep Comdt', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-6', 'max_grade' => 'OF-7'],
                ['title' => 'Chief of Staff', 'abbreviation' => 'COS', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-5', 'max_grade' => 'OF-6'],
                ['title' => 'Staff Officer Operations', 'abbreviation' => 'SO Ops', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Staff Officer Logistics', 'abbreviation' => 'SO Log', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Staff Officer Personnel', 'abbreviation' => 'SO Pers', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Staff Officer Training', 'abbreviation' => 'SO Trg', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Fleet Chief Petty Officer', 'abbreviation' => 'FCPO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-2', 'max_grade' => 'WO-2'],
            ],
            NodeType::Base->value => [
                ['title' => 'Officer Commanding', 'abbreviation' => 'OC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-5', 'max_grade' => 'OF-6'],
                ['title' => 'Executive Officer', 'abbreviation' => 'XO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Base Warrant Officer', 'abbreviation' => 'BWO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-2', 'max_grade' => 'WO-2'],
            ],
            NodeType::Flotilla->value => [
                ['title' => 'Commander Flotilla', 'abbreviation' => 'Comd Flot', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-5', 'max_grade' => 'OF-5'],
                ['title' => 'Operations Officer', 'abbreviation' => 'Ops O', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
            ],
            NodeType::Vessel->value => [
                ['title' => 'Commanding Officer', 'abbreviation' => 'CO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-3', 'max_grade' => 'OF-5'],
                ['title' => 'Executive Officer', 'abbreviation' => 'XO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-2', 'max_grade' => 'OF-4'],
                ['title' => 'Navigating Officer', 'abbreviation' => 'Nav O', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Technical, 'is_command' => false, 'min_grade' => 'OF-1', 'max_grade' => 'OF-3'],
                ['title' => 'Chief Petty Officer', 'abbreviation' => 'CPO', 'category' => AppointmentCategory::OtherRanks, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'OR-5', 'max_grade' => 'OR-5'],
            ],
            NodeType::Station->value => [
                ['title' => 'Officer Commanding', 'abbreviation' => 'OC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Executive Officer', 'abbreviation' => 'XO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
            ],
            NodeType::Department->value => [
                ['title' => 'Head of Department', 'abbreviation' => 'HOD', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
            ],
        ];
    }
}
