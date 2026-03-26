<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Models\Formation;

class TtagUnitSeeder extends AbstractUnitSeeder
{
    protected function defaultServiceBranch(): ServiceBranch
    {
        return ServiceBranch::Air;
    }

    public function run(): void
    {
        $this->formation = Formation::where('abbreviation', 'TTAG')->firstOrFail();

        // ----- Air Guard (Formation root) -----
        $ttag = $this->make('TTAG', [
            'name' => 'Trinidad and Tobago Air Guard',
            'abbreviation' => 'TTAG',
            'node_type' => NodeType::Formation,
            'parent_id' => null,
            'sort_order' => 0,
        ]);

        // ----- Air Guard HQ -----
        $hq = $this->make('AGHQ', [
            'name' => 'Air Guard Headquarters',
            'abbreviation' => 'AG HQ',
            'node_type' => NodeType::Headquarters,
            'parent_id' => $ttag->id,
            'sort_order' => 1,
        ]);

        $this->make('AGHQOPS', [
            'name' => 'Operations Branch',
            'abbreviation' => 'Ops Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 1,
        ]);

        $this->make('AGHQLOG', [
            'name' => 'Logistics Branch',
            'abbreviation' => 'Log Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 2,
        ]);

        $this->make('AGHQTRG', [
            'name' => 'Training Branch',
            'abbreviation' => 'Trg Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 3,
        ]);

        // ----- 1st Air Guard Squadron (Piarco) -----
        $sq1 = $this->make('1AGS', [
            'name' => '1st Air Guard Squadron',
            'abbreviation' => '1 AGS',
            'node_type' => NodeType::Squadron,
            'parent_id' => $ttag->id,
            'sort_order' => 2,
        ]);

        $this->make('1AGSTRNFLT', [
            'name' => 'Transport Flight',
            'abbreviation' => 'Tpt Flt',
            'node_type' => NodeType::Flight,
            'parent_id' => $sq1->id,
            'sort_order' => 1,
        ]);

        $this->make('1AGSHELFLT', [
            'name' => 'Helicopter Flight',
            'abbreviation' => 'Hel Flt',
            'node_type' => NodeType::Flight,
            'parent_id' => $sq1->id,
            'sort_order' => 2,
        ]);

        $this->make('1AGSSURFLT', [
            'name' => 'Surveillance Flight',
            'abbreviation' => 'Surv Flt',
            'node_type' => NodeType::Flight,
            'parent_id' => $sq1->id,
            'sort_order' => 3,
        ]);

        // ----- Technical Wing -----
        $techWing = $this->make('AGTWING', [
            'name' => 'Technical Wing',
            'abbreviation' => 'Tech Wg',
            'node_type' => NodeType::Wing,
            'parent_id' => $ttag->id,
            'sort_order' => 3,
        ]);

        $this->make('AGAMFLT', [
            'name' => 'Aircraft Maintenance Flight',
            'abbreviation' => 'AM Flt',
            'node_type' => NodeType::Flight,
            'parent_id' => $techWing->id,
            'sort_order' => 1,
        ]);

        $this->make('AGGSFLT', [
            'name' => 'Ground Support Flight',
            'abbreviation' => 'GS Flt',
            'node_type' => NodeType::Flight,
            'parent_id' => $techWing->id,
            'sort_order' => 2,
        ]);

        // ----- Air Guard Station Tobago (Crown Point) -----
        $this->make('AGST', [
            'name' => 'Air Guard Station Tobago',
            'abbreviation' => 'AGS Tobago',
            'node_type' => NodeType::Station,
            'parent_id' => $ttag->id,
            'sort_order' => 4,
        ]);
    }
}
