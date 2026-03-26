<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Enums\NodeType;

class TtrAppointmentSeeder extends AbstractAppointmentSeeder
{
    protected function formationAbbreviation(): string
    {
        return 'TTR';
    }

    /**
     * @return array<string, list<array{title: string, abbreviation: string, category: AppointmentCategory, type: AppointmentType, is_command: bool, min_grade: string|null, max_grade: string|null}>>
     */
    protected function templates(): array
    {
        return [
            NodeType::Formation->value => [
                ['title' => 'Commanding Officer', 'abbreviation' => 'CO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-7', 'max_grade' => 'OF-8'],
                ['title' => 'Deputy Commanding Officer', 'abbreviation' => 'DCO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-6', 'max_grade' => 'OF-7'],
                ['title' => 'Senior Staff Officer', 'abbreviation' => 'SSO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-5', 'max_grade' => 'OF-6'],
                ['title' => 'Personnel Officer', 'abbreviation' => 'G1', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Legal Officer', 'abbreviation' => 'LO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Human Resource Officer', 'abbreviation' => 'HRO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Education Officer', 'abbreviation' => 'EO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Intelligence Officer', 'abbreviation' => 'G2', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Operations Officer', 'abbreviation' => 'G3', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Logistics Officer', 'abbreviation' => 'G4', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Projects Officer', 'abbreviation' => 'G5', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'ICT Officer', 'abbreviation' => 'G6', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Regiment Signals Officer', 'abbreviation' => 'RSO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Public Relations Officer', 'abbreviation' => 'G8', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Welfare Officer', 'abbreviation' => 'G9', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Regimental Command Warrant Officer', 'abbreviation' => 'RCWO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-2', 'max_grade' => 'WO-2'],
            ],
            NodeType::Battalion->value => [
                ['title' => 'Commanding Officer', 'abbreviation' => 'CO', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-5', 'max_grade' => 'OF-5'],
                ['title' => 'Second in Command', 'abbreviation' => '2IC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-4'],
                ['title' => 'Adjutant', 'abbreviation' => 'Adjt', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-3'],
                ['title' => 'Logistics Officer', 'abbreviation' => 'S4', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
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
