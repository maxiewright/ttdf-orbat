<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use MaxieWright\TtdfOrbat\Database\Factories\FormationFactory;
use MaxieWright\TtdfOrbat\Enums\FormationType;

/**
 * @property int $id
 * @property string $name
 * @property string $abbreviation
 * @property FormationType $type
 * @property bool $is_active
 * @property-read Collection<int, Appointment> $appointments
 */
class Formation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): Factory
    {
        return FormationFactory::new();
    }

    protected function casts(): array
    {
        return [
            'type' => FormationType::class,
            'is_active' => 'boolean',
        ];
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class)
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }

    public function allUnits(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

    public function appointments(): HasManyThrough
    {
        return $this->hasManyThrough(Appointment::class, Unit::class);
    }

    public function rankGrades(): HasManyThrough
    {
        return $this->hasManyThrough(RankGrade::class, Rank::class, 'formation_id', 'id', 'id', 'rank_grade_id');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function ofType(Builder $query, FormationType $type): void
    {
        $query->where('type', $type);
    }

    protected function shortName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->abbreviation,
        );
    }
}
