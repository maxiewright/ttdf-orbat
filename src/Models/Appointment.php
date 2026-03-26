<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use MaxieWright\TtdfOrbat\Enums\AppointmentCategory;
use MaxieWright\TtdfOrbat\Enums\AppointmentType;

/**
 * @property int $id
 * @property int $unit_id
 * @property int|null $min_rank_grade_id
 * @property int|null $max_rank_grade_id
 * @property string $title
 * @property string $abbreviation
 * @property AppointmentCategory $category
 * @property AppointmentType $type
 * @property bool $is_command
 * @property bool $is_active
 * @property int $sort_order
 * @property string|null $remarks
 * @property-read Unit $unit
 * @property-read RankGrade|null $minRankGrade
 * @property-read RankGrade|null $maxRankGrade
 * @property-read string $rank_range
 * @property-read string $full_title
 */
class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'category' => AppointmentCategory::class,
            'type' => AppointmentType::class,
            'is_command' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function minRankGrade(): BelongsTo
    {
        return $this->belongsTo(RankGrade::class, 'min_rank_grade_id');
    }

    public function maxRankGrade(): BelongsTo
    {
        return $this->belongsTo(RankGrade::class, 'max_rank_grade_id');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function command(Builder $query): void
    {
        $query->where('is_command', true);
    }

    #[Scope]
    protected function forUnit(Builder $query, int|Unit $unit): void
    {
        $query->where('unit_id', $unit instanceof Unit ? $unit->id : $unit);
    }

    #[Scope]
    protected function byCategory(Builder $query, AppointmentCategory $category): void
    {
        $query->where('category', $category);
    }

    #[Scope]
    protected function byType(Builder $query, AppointmentType $type): void
    {
        $query->where('type', $type);
    }

    // ---------------------------------------------------------------
    // Accessors
    // ---------------------------------------------------------------

    protected function rankRange(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $min = $this->minRankGrade;
                $max = $this->maxRankGrade;

                if ($min === null && $max === null) {
                    return '—';
                }

                if ($min !== null && $max !== null && $min->id !== $max->id) {
                    return "{$min->code} – {$max->code}";
                }

                return ($min ?? $max)->code;
            },
        );
    }

    protected function fullTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): string => "{$this->abbreviation} ({$this->title})",
        );
    }
}
