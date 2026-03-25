<?php

namespace MaxieWright\TtdfOrbat;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

class TtdfOrbat
{
    public const VERSION = '0.1.0';

    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function formations(): Collection
    {
        return $this->cached('formations', fn () => Formation::active()->get());
    }

    public function formation(string $abbreviation): ?Formation
    {
        /** @var Formation|null */
        return $this->formations()->firstWhere('abbreviation', $abbreviation);
    }

    /**
     * @return Collection<int, Rank>
     */
    public function ranks(string $formationAbbreviation): Collection
    {
        return $this->cached("ranks.{$formationAbbreviation}", function () use ($formationAbbreviation) {
            $formation = Formation::where('abbreviation', $formationAbbreviation)->first();

            if (! $formation) {
                return new Collection;
            }

            return Rank::with('grade')
                ->where('formation_id', $formation->id)
                ->orderBy(
                    RankGrade::select('level')
                        ->whereColumn('rank_grades.id', 'ranks.rank_grade_id')
                )
                ->get();
        });
    }

    /**
     * @return Collection<int, RankGrade>
     */
    public function grades(): Collection
    {
        return $this->cached('grades', fn () => RankGrade::orderBy('category')->orderBy('level')->get());
    }

    /**
     * @return Collection<int, Rank>
     */
    public function officers(string $formationAbbreviation): Collection
    {
        return $this->ranks($formationAbbreviation)->filter(
            fn (Rank $rank) => $rank->grade->category === RankCategory::Commissioned
        )->values();
    }

    /**
     * @return Collection<int, Rank>
     */
    public function otherRanks(string $formationAbbreviation): Collection
    {
        return $this->ranks($formationAbbreviation)->filter(
            fn (Rank $rank) => $rank->grade->category === RankCategory::OtherRanks
        )->values();
    }

    /**
     * @return Collection<int, Unit>
     */
    public function tree(string $formationAbbreviation): Collection
    {
        $formation = Formation::where('abbreviation', $formationAbbreviation)->first();

        if (! $formation) {
            return new Collection;
        }

        return Unit::with('children')
            ->where('formation_id', $formation->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Unit>
     */
    public function units(string $formationAbbreviation): Collection
    {
        $formation = Formation::where('abbreviation', $formationAbbreviation)->first();

        if (! $formation) {
            return new Collection;
        }

        return Unit::active()->where('formation_id', $formation->id)->get();
    }

    private function cached(string $key, callable $resolver): mixed
    {
        /** @var int $ttl */
        $ttl = config('ttdf-orbat.cache_ttl', 3600);

        if ($ttl <= 0) {
            return $resolver();
        }

        return Cache::remember("ttdf-orbat.{$key}", $ttl, $resolver);
    }
}
