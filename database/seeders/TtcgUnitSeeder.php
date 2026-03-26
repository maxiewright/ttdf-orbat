<?php

namespace MaxieWright\TtdfOrbat\Database\Seeders;

use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Models\Formation;

class TtcgUnitSeeder extends AbstractUnitSeeder
{
    protected function defaultServiceBranch(): ServiceBranch
    {
        return ServiceBranch::Navy;
    }

    public function run(): void
    {
        $this->formation = Formation::where('abbreviation', 'TTCG')->firstOrFail();

        // ----- Coast Guard (Formation root) -----
        $ttcg = $this->make('TTCG', [
            'name' => 'Trinidad and Tobago Coast Guard',
            'abbreviation' => 'TTCG',
            'node_type' => NodeType::Formation,
            'parent_id' => null,
            'sort_order' => 0,
        ]);

        // ----- Coast Guard HQ -----
        $hq = $this->make('CGTHQ', [
            'name' => 'Coast Guard Headquarters',
            'abbreviation' => 'CG HQ',
            'node_type' => NodeType::Headquarters,
            'parent_id' => $ttcg->id,
            'sort_order' => 1,
        ]);

        $this->make('CGTHQOPS', [
            'name' => 'Operations Branch',
            'abbreviation' => 'Ops Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 1,
        ]);

        $this->make('CGTHQLOG', [
            'name' => 'Logistics Branch',
            'abbreviation' => 'Log Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 2,
        ]);

        $this->make('CGTHQTRG', [
            'name' => 'Training Branch',
            'abbreviation' => 'Trg Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 3,
        ]);

        $this->make('CGTHQINT', [
            'name' => 'Intelligence Branch',
            'abbreviation' => 'Int Br',
            'node_type' => NodeType::Branch,
            'parent_id' => $hq->id,
            'sort_order' => 4,
        ]);

        // ----- Coast Guard Base (Staubles Bay) -----
        $cgb = $this->make('CGB', [
            'name' => 'Coast Guard Base',
            'abbreviation' => 'CG Base',
            'node_type' => NodeType::Base,
            'parent_id' => $ttcg->id,
            'sort_order' => 2,
        ]);

        $this->make('CGBOPS', [
            'name' => 'Operations Department',
            'abbreviation' => 'Ops Dept',
            'node_type' => NodeType::Department,
            'parent_id' => $cgb->id,
            'sort_order' => 1,
        ]);

        $this->make('CGBLOG', [
            'name' => 'Logistics Department',
            'abbreviation' => 'Log Dept',
            'node_type' => NodeType::Department,
            'parent_id' => $cgb->id,
            'sort_order' => 2,
        ]);

        $this->make('CGBTRG', [
            'name' => 'Coast Guard School',
            'abbreviation' => 'CG School',
            'node_type' => NodeType::Department,
            'parent_id' => $cgb->id,
            'sort_order' => 3,
        ]);

        // ----- Flotilla 1 (active patrol fleet) -----
        $flot1 = $this->make('FLOT1', [
            'name' => '1st Flotilla',
            'abbreviation' => 'Flot 1',
            'node_type' => NodeType::Flotilla,
            'parent_id' => $ttcg->id,
            'sort_order' => 3,
        ]);

        $vessels = [
            ['TTS-01', 'TTS Nelson',        'TTS Nelson',      1],
            ['TTS-02', 'TTS Point Lisas',   'TTS Point Lisas', 2],
            ['TTS-03', 'TTS Buccoo Reef',   'TTS Buccoo Reef', 3],
            ['TTS-04', 'TTS Moruga',        'TTS Moruga',      4],
            ['TTS-05', 'TTS Nariva',        'TTS Nariva',      5],
            ['TTS-06', 'TTS Corozal',       'TTS Corozal',     6],
        ];

        foreach ($vessels as [$code, $name, $abbreviation, $order]) {
            $this->make($code, [
                'name' => $name,
                'abbreviation' => $abbreviation,
                'node_type' => NodeType::Vessel,
                'parent_id' => $flot1->id,
                'sort_order' => $order,
            ]);
        }

        // ----- Coast Guard Station Tobago -----
        $this->make('CGST', [
            'name' => 'Coast Guard Station Tobago',
            'abbreviation' => 'CGS Tobago',
            'node_type' => NodeType::Station,
            'parent_id' => $ttcg->id,
            'sort_order' => 4,
        ]);
    }
}
