<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $unit_id
 * @property int $attached_to_id
 * @property Carbon $effective_from
 * @property Carbon|null $effective_to
 * @property string|null $authority
 * @property string|null $remarks
 * @property-read Unit $unit
 * @property-read Unit $attachedTo
 */
class UnitAttachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function attachedTo(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'attached_to_id');
    }

    #[Scope]
    protected function current(Builder $query): void
    {
        $query->whereNull('effective_to');
    }

    #[Scope]
    protected function historical(Builder $query): void
    {
        $query->whereNotNull('effective_to');
    }

    #[Scope]
    protected function forUnit(Builder $query, int|Unit $unit): void
    {
        $query->where('unit_id', $unit instanceof Unit ? $unit->id : $unit);
    }

    #[Scope]
    protected function atUnit(Builder $query, int|Unit $unit): void
    {
        $query->where('attached_to_id', $unit instanceof Unit ? $unit->id : $unit);
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->effective_to === null,
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->effective_to !== null
                ? $this->effective_from->diff($this->effective_to)->forHumans()
                : null,
        );
    }

    public function detach(?string $authority = null): bool
    {
        $this->effective_to = Carbon::today();

        if ($authority !== null) {
            $this->remarks = $this->remarks
                ? "{$this->remarks}\nDetached: {$authority}"
                : "Detached: {$authority}";
        }

        return $this->save();
    }
}
