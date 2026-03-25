<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MaxieWright\TtdfOrbat\Enums\RankCategory;

/**
 * @property int $id
 * @property string $code
 * @property string|null $nato_code
 * @property RankCategory $category
 * @property int $level
 * @property string $label
 */
class RankGrade extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'category' => RankCategory::class,
        ];
    }

    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

    /** @return Rank|null */
    public function titleFor(Formation $formation): ?Rank
    {
        /** @var Rank|null */
        return $this->ranks()->where('formation_id', $formation->id)->first();
    }

    #[Scope]
    protected function officers(Builder $query): void
    {
        $query->where('category', RankCategory::Commissioned);
    }

    #[Scope]
    protected function otherRanks(Builder $query): void
    {
        $query->where('category', RankCategory::OtherRanks);
    }

    #[Scope]
    protected function warranted(Builder $query): void
    {
        $query->where('category', RankCategory::Warrant);
    }

    #[Scope]
    protected function ofLevel(Builder $query, int $level): void
    {
        $query->where('level', $level);
    }

    #[Scope]
    protected function atLeast(Builder $query, int $level): void
    {
        $query->where('level', '>=', $level);
    }

    protected function isOfficer(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category === RankCategory::Commissioned,
        );
    }

    protected function isWarrant(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category === RankCategory::Warrant,
        );
    }

    protected function isEnlisted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category === RankCategory::OtherRanks,
        );
    }
}
