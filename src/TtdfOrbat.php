<?php

namespace MaxieWright\TtdfOrbat;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use MaxieWright\TtdfOrbat\Enums\RankCategory;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

class TtdfOrbat
{
    public const VERSION = '0.2.0';

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
            $formation = $this->resolveFormation($formationAbbreviation);

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
        $formation = $this->resolveFormation($formationAbbreviation);

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
        $formation = $this->resolveFormation($formationAbbreviation);

        if (! $formation) {
            return new Collection;
        }

        return Unit::active()->where('formation_id', $formation->id)->get();
    }

    /**
     * All active appointments for a unit, ordered by sort_order.
     * Accepts the unit abbreviation (e.g. '1TTR', 'A Coy') or a Unit model.
     *
     * @return Collection<int, Appointment>
     */
    public function appointments(string|Unit $unit): Collection
    {
        $unitModel = $this->resolveUnit($unit);

        if (! $unitModel) {
            return new Collection;
        }

        return $this->cached("appointments.{$unitModel->id}", fn () => Appointment::with(['minRankGrade', 'maxRankGrade'])
            ->active()
            ->where('unit_id', $unitModel->id)
            ->orderBy('sort_order')
            ->get()
        );
    }

    /**
     * Command appointments only for a given unit.
     *
     * @return Collection<int, Appointment>
     */
    public function commandAppointments(string|Unit $unit): Collection
    {
        return $this->appointments($unit)->where('is_command', true)->values();
    }

    private function resolveFormation(string $abbreviation): ?Formation
    {
        return Formation::where('abbreviation', $abbreviation)->first();
    }

    private function resolveUnit(string|Unit $unit): ?Unit
    {
        if ($unit instanceof Unit) {
            return $unit;
        }

        return Unit::where('abbreviation', $unit)->first();
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
