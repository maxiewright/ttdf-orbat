<?php

namespace MaxieWright\TtdfOrbat\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\OrbatAudience;
use MaxieWright\TtdfOrbat\Models\RankGrade;
use MaxieWright\TtdfOrbat\Models\Unit;

/**
 * Add to any consumer model (e.g. Notice, Document, Event) to make it
 * audience-targetable by ORBAT entities (Formation, Unit, RankGrade).
 *
 * Requires the orbat_audiences table, which is auto-migrated by the package.
 */
trait HasOrbatAudience
{
    public function orbatAudiences(): MorphMany
    {
        return $this->morphMany(OrbatAudience::class, 'auditable');
    }

    public function addAudience(Formation|Unit|RankGrade $target): OrbatAudience
    {
        return $this->orbatAudiences()->firstOrCreate($this->targetableKey($target));
    }

    public function removeAudience(Formation|Unit|RankGrade $target): void
    {
        $this->orbatAudiences()->where($this->targetableKey($target))->delete();
    }

    public function isTargetedAt(Formation|Unit|RankGrade $target): bool
    {
        return $this->orbatAudiences()->where($this->targetableKey($target))->exists();
    }

    public function scopeForAudience(Builder $query, Formation|Unit|RankGrade $target): void
    {
        $key = $this->targetableKey($target);

        $query->whereHas('orbatAudiences', function (Builder $q) use ($key): void {
            $q->where($key);
        });
    }

    /** @return array{targetable_type: string, targetable_id: int|string} */
    private function targetableKey(Formation|Unit|RankGrade $target): array
    {
        return [
            'targetable_type' => $target->getMorphClass(),
            'targetable_id' => $target->getKey(),
        ];
    }
}
