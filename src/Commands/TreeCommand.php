<?php

namespace MaxieWright\TtdfOrbat\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Unit;

class TreeCommand extends Command
{
    public $signature = 'ttdf-orbat:tree {formation : Formation abbreviation (e.g. TTR, TTCG, TTAG)}
                         {--depth=0 : Maximum tree depth to display (0 = unlimited)}
                         {--status=active : Filter by status (active, all)}';

    public $description = 'Render the unit hierarchy for a formation as a tree';

    public function handle(): int
    {
        $abbreviation = strtoupper($this->argument('formation'));
        $formation = Formation::where('abbreviation', $abbreviation)->first();

        if (! $formation) {
            $this->components->error("Formation '{$abbreviation}' not found.");
            $this->components->bulletList(
                Formation::pluck('abbreviation')->map(fn (string $a) => $a)->toArray()
            );

            return self::FAILURE;
        }

        $maxDepth = (int) $this->option('depth');
        $showAll = $this->option('status') === 'all';

        $query = Unit::where('formation_id', $formation->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order');

        if (! $showAll) {
            $query->where('status', 'active');
        }

        $roots = $query->get();

        if ($roots->isEmpty()) {
            $this->components->warn("No units found for {$abbreviation}.");

            return self::SUCCESS;
        }

        $this->components->info("{$formation->name} ({$abbreviation})");
        $this->newLine();

        foreach ($roots as $root) {
            $this->renderNode($root, '', true, $maxDepth, 0, $showAll);
        }

        return self::SUCCESS;
    }

    private function renderNode(Unit $unit, string $prefix, bool $isLast, int $maxDepth, int $currentDepth, bool $showAll): void
    {
        $connector = $currentDepth === 0 ? '' : ($isLast ? '└── ' : '├── ');
        $label = $unit->abbreviation ?? $unit->name;
        $type = $unit->node_type->label();
        $status = $unit->status->value !== 'active' ? " [{$unit->status->value}]" : '';

        $this->line("{$prefix}{$connector}<info>{$label}</info> <comment>({$type})</comment>{$status}");

        if ($maxDepth > 0 && $currentDepth >= $maxDepth) {
            return;
        }

        $query = $unit->children()->orderBy('sort_order');

        if (! $showAll) {
            $query->where('status', 'active');
        }

        $children = $query->get();

        $childPrefix = $prefix . ($currentDepth === 0 ? '' : ($isLast ? '    ' : '│   '));

        foreach ($children as $index => $child) {
            /** @var Unit $child */
            $this->renderNode($child, $childPrefix, $index === $children->count() - 1, $maxDepth, $currentDepth + 1, $showAll);
        }
    }
}
