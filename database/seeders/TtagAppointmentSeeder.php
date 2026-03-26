<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;
use MaxieWright\TtdfOrbat\Enums\NodeType;

class TtagAppointmentSeeder extends AbstractAppointmentSeeder
{
    protected function formationAbbreviation(): string
    {
        return 'TTAG';
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
                ['title' => 'Chief of Staff', 'abbreviation' => 'COS', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-5', 'max_grade' => 'OF-6'],
                ['title' => 'Staff Officer Operations', 'abbreviation' => 'SO Ops', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Staff Officer Logistics', 'abbreviation' => 'SO Log', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Staff Officer Personnel', 'abbreviation' => 'SO Pers', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Staff Officer Training', 'abbreviation' => 'SO Trg', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Staff, 'is_command' => false, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Air Guard Warrant Officer', 'abbreviation' => 'AGWO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-2', 'max_grade' => 'WO-2'],
            ],
            NodeType::Squadron->value => [
                ['title' => 'Officer Commanding', 'abbreviation' => 'OC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-5', 'max_grade' => 'OF-5'],
                ['title' => 'Second in Command', 'abbreviation' => '2IC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OF-4', 'max_grade' => 'OF-4'],
                ['title' => 'Squadron Warrant Officer', 'abbreviation' => 'SqnWO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-1', 'max_grade' => 'WO-2'],
            ],
            NodeType::Wing->value => [
                ['title' => 'Wing Commander', 'abbreviation' => 'Wg Comd', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-5', 'max_grade' => 'OF-5'],
                ['title' => 'Wing Warrant Officer', 'abbreviation' => 'WgWO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-1', 'max_grade' => 'WO-1'],
            ],
            NodeType::Flight->value => [
                ['title' => 'Flight Commander', 'abbreviation' => 'Flt Comd', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-3', 'max_grade' => 'OF-4'],
                ['title' => 'Flight Sergeant', 'abbreviation' => 'Flt Sgt', 'category' => AppointmentCategory::OtherRanks, 'type' => AppointmentType::Command, 'is_command' => false, 'min_grade' => 'OR-5', 'max_grade' => 'OR-5'],
            ],
            NodeType::Station->value => [
                ['title' => 'Officer Commanding', 'abbreviation' => 'OC', 'category' => AppointmentCategory::Commissioned, 'type' => AppointmentType::Command, 'is_command' => true, 'min_grade' => 'OF-4', 'max_grade' => 'OF-5'],
                ['title' => 'Station Warrant Officer', 'abbreviation' => 'StnWO', 'category' => AppointmentCategory::WarrantOfficer, 'type' => AppointmentType::Administrative, 'is_command' => false, 'min_grade' => 'WO-1', 'max_grade' => 'WO-1'],
            ],
        ];
    }
}
