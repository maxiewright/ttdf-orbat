<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MaxieWright\TtdfOrbat\Database\Factories\RankFactory;

/**
 * @property int $id
 * @property int $rank_grade_id
 * @property int $formation_id
 * @property string $title
 * @property string $abbreviation
 * @property string|null $insignia_path
 * @property-read RankGrade|null $grade
 */
class Rank extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): Factory
    {
        return RankFactory::new();
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(RankGrade::class, 'rank_grade_id');
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    #[Scope]
    protected function forFormation(Builder $query, int|Formation $formation): void
    {
        $query->where('formation_id', $formation instanceof Formation ? $formation->id : $formation);
    }

    #[Scope]
    protected function officers(Builder $query): void
    {
        $query->whereHas('grade', fn (Builder $q) => $q->where('category', 'OF'));
    }

    #[Scope]
    protected function otherRanks(Builder $query): void
    {
        $query->whereHas('grade', fn (Builder $q) => $q->where('category', 'OR'));
    }

    protected function fullTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->abbreviation} ({$this->title})",
        );
    }

    protected function natoCode(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->grade?->nato_code,
        );
    }

    /**
     * @return array<int, string>
     */
    public static function optionsForFormation(int $formationId): array
    {
        return static::query()
            ->with('grade')
            ->where('formation_id', $formationId)
            ->get()
            ->sortBy('grade.level')
            ->pluck('title', 'id')
            ->all();
    }
}
