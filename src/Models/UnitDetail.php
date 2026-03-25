<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use MaxieWright\TtdfOrbat\Enums\VesselType;

/**
 * @property int $id
 * @property string $detailable_type
 * @property int $detailable_id
 * @property string|null $hull_number
 * @property VesselType|null $vessel_type
 * @property string|null $vessel_class
 * @property string|null $pennant_prefix
 * @property int|null $displacement_tons
 * @property int|null $crew_complement
 * @property Carbon|null $commissioned_at
 * @property Carbon|null $decommissioned_at
 * @property string|null $base_location
 * @property string|null $base_type
 * @property string|null $grid_reference
 * @property float|null $latitude
 * @property float|null $longitude
 */
class UnitDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'vessel_type' => VesselType::class,
            'commissioned_at' => 'date',
            'decommissioned_at' => 'date',
        ];
    }

    public function detailable(): MorphTo
    {
        return $this->morphTo();
    }

    #[Scope]
    protected function vessels(Builder $query): void
    {
        $query->whereNotNull('hull_number');
    }

    #[Scope]
    protected function activeVessels(Builder $query): void
    {
        $query->whereNotNull('hull_number')->whereNull('decommissioned_at');
    }

    #[Scope]
    protected function bases(Builder $query): void
    {
        $query->whereNotNull('base_location');
    }

    #[Scope]
    protected function withLocation(Builder $query): void
    {
        $query->whereNotNull('latitude');
    }

    protected function pennantNumber(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->pennant_prefix !== null && $this->hull_number !== null
                ? "{$this->pennant_prefix}-{$this->hull_number}"
                : null,
        );
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->decommissioned_at === null,
        );
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->vessel_type?->label(),
        );
    }
}
