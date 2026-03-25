<?php

namespace MaxieWright\TtdfOrbat\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;
use MaxieWright\TtdfOrbat\Models\UnitAttachment;

class ValidateCommand extends Command
{
    public $signature = 'ttdf-orbat:validate';

    public $description = 'Validate ORBAT data integrity and report issues';

    /** @var array<string> */
    private array $issues = [];

    /** @var array<string> */
    private array $warnings = [];

    public function handle(): int
    {
        $this->components->info('Validating ORBAT data...');

        $this->checkOrphanedUnits();
        $this->checkRankCoverage();
        $this->checkDetachmentAttachments();
        $this->checkDuplicateAttachments();
        $this->checkEmptyFormations();

        if (empty($this->issues) && empty($this->warnings)) {
            $this->components->info('All checks passed. No issues found.');

            return self::SUCCESS;
        }

        if (! empty($this->warnings)) {
            $this->newLine();
            $this->components->warn('Warnings ('.count($this->warnings).')');

            foreach ($this->warnings as $warning) {
                $this->line("  ⚠ {$warning}");
            }
        }

        if (! empty($this->issues)) {
            $this->newLine();
            $this->components->error('Issues ('.count($this->issues).')');

            foreach ($this->issues as $issue) {
                $this->line("  ✗ {$issue}");
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function checkOrphanedUnits(): void
    {
        $orphans = Unit::whereNotNull('parent_id')
            ->whereDoesntHave('parent')
            ->get();

        foreach ($orphans as $unit) {
            $this->issues[] = "Unit '{$unit->name}' (ID:{$unit->id}) references non-existent parent_id {$unit->parent_id}";
        }
    }

    private function checkRankCoverage(): void
    {
        $gradeCount = RankGrade::count();

        if ($gradeCount === 0) {
            $this->warnings[] = 'No rank grades found. Run the seeder first.';

            return;
        }

        $formations = Formation::all();

        foreach ($formations as $formation) {
            $rankCount = Rank::where('formation_id', $formation->id)->count();

            if ($rankCount === 0) {
                $this->warnings[] = "Formation '{$formation->abbreviation}' has no ranks seeded.";
            } elseif ($rankCount < $gradeCount) {
                $missing = $gradeCount - $rankCount;
                $this->warnings[] = "Formation '{$formation->abbreviation}' is missing {$missing} rank(s) out of {$gradeCount} grades.";
            }
        }
    }

    private function checkDetachmentAttachments(): void
    {
        $detachments = Unit::where('node_type', 'detachment')
            ->where('status', 'active')
            ->get();

        foreach ($detachments as $det) {
            $hasAttachment = UnitAttachment::where('unit_id', $det->id)
                ->whereNull('effective_to')
                ->exists();

            if (! $hasAttachment) {
                $this->warnings[] = "Active detachment '{$det->name}' (ID:{$det->id}) has no current attachment.";
            }
        }
    }

    private function checkDuplicateAttachments(): void
    {
        /** @var Collection<int, object{unit_id: int, cnt: int}> $duplicates */
        $duplicates = UnitAttachment::whereNull('effective_to')
            ->selectRaw('unit_id, COUNT(*) as cnt')
            ->groupBy('unit_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $row) {
            $unit = Unit::find($row->unit_id);
            $name = $unit->name ?? "ID:{$row->unit_id}";
            $this->issues[] = "Unit '{$name}' has {$row->cnt} active attachments (expected at most 1).";
        }
    }

    private function checkEmptyFormations(): void
    {
        $formations = Formation::where('is_active', true)
            ->withCount('allUnits')
            ->get()
            ->where('all_units_count', 0);

        foreach ($formations as $formation) {
            $this->warnings[] = "Active formation '{$formation->abbreviation}' has no units.";
        }
    }
}
