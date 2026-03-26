<?php

namespace MaxieWright\TtdfOrbat\Commands;

use Illuminate\Console\Command;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

class StatsCommand extends Command
{
    public $signature = 'ttdf-orbat:stats';

    public $description = 'Display a summary of seeded ORBAT data';

    public function handle(): int
    {
        $this->components->info('TTDF ORBAT Statistics');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Formations', Formation::count()],
                ['Rank Grades', RankGrade::count()],
                ['Ranks (total)', Rank::count()],
                ['Units (total)', Unit::count()],
                ['Units (active)', Unit::where('status', 'active')->count()],
                ['Top-level Units', Unit::whereNull('parent_id')->count()],
                ['Active Attachments', UnitAttachment::whereNull('effective_to')->count()],
                ['Appointments (total)', Appointment::count()],
                ['Appointments (command)', Appointment::where('is_command', true)->count()],
            ],
        );

        $formations = Formation::withCount('allUnits', 'ranks')->get();

        if ($formations->isNotEmpty()) {
            $this->newLine();
            $this->components->info('Per-Formation Breakdown');

            $this->table(
                ['Formation', 'Abbreviation', 'Units', 'Ranks'],
                $formations->map(fn (Formation $f) => [
                    $f->name,
                    $f->abbreviation,
                    $f->all_units_count,
                    $f->ranks_count,
                ])->toArray(),
            );
        }

        return self::SUCCESS;
    }
}
