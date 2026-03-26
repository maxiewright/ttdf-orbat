<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use MaxieWright\TtdfOrbat\Database\Factories\UnitFactory;
use MaxieWright\TtdfOrbat\Enums\NodeType;
use MaxieWright\TtdfOrbat\Enums\ServiceBranch;
use MaxieWright\TtdfOrbat\Enums\UnitStatus;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * @property int $id
 * @property int $formation_id
 * @property int|null $parent_id
 * @property NodeType $node_type
 * @property ServiceBranch|null $service_branch
 * @property string $name
 * @property string|null $abbreviation
 * @property string|null $designation
 * @property UnitStatus $status
 * @property int $sort_order
 * @property Carbon|null $activated_at
 * @property Carbon|null $deactivated_at
 * @property-read Unit|null $parent
 * @property-read UnitDetail|null $detail
 * @property-read UnitAttachment|null $currentAttachment
 * @property-read Collection<int, Appointment> $appointments
 */
class Unit extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;
    use SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): Factory
    {
        return UnitFactory::new();
    }

    protected function casts(): array
    {
        return [
            'node_type' => NodeType::class,
            'service_branch' => ServiceBranch::class,
            'status' => UnitStatus::class,
            'activated_at' => 'date',
            'deactivated_at' => 'date',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function detail(): MorphOne
    {
        return $this->morphOne(UnitDetail::class, 'detailable');
    }

    public function currentAttachment(): HasOne
    {
        return $this->hasOne(UnitAttachment::class)
            ->whereNull('effective_to')
            ->latestOfMany('effective_from');
    }

    public function attachmentHistory(): HasMany
    {
        return $this->hasMany(UnitAttachment::class)->latest('effective_from');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('sort_order');
    }

    public function attachedUnits(): HasMany
    {
        return $this->hasMany(UnitAttachment::class, 'attached_to_id')
            ->whereNull('effective_to')
            ->with('unit');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', UnitStatus::Active);
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', '!=', UnitStatus::Active);
    }

    #[Scope]
    protected function ofType(Builder $query, NodeType $type): void
    {
        $query->where('node_type', $type);
    }

    #[Scope]
    protected function topLevel(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    #[Scope]
    protected function vessels(Builder $query): void
    {
        $query->where('node_type', NodeType::Vessel);
    }

    #[Scope]
    protected function detachments(Builder $query): void
    {
        $query->where('node_type', NodeType::Detachment);
    }

    #[Scope]
    protected function departments(Builder $query): void
    {
        $query->where('node_type', NodeType::Department);
    }

    #[Scope]
    protected function forService(Builder $query, ServiceBranch $branch): void
    {
        $query->where('service_branch', $branch);
    }

    #[Scope]
    protected function forFormation(Builder $query, int|Formation $formation): void
    {
        $query->where('formation_id', $formation instanceof Formation ? $formation->id : $formation);
    }

    // ---------------------------------------------------------------
    // Accessors
    // ---------------------------------------------------------------

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->abbreviation ?? $this->name,
        );
    }

    protected function isVessel(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->node_type === NodeType::Vessel,
        );
    }

    protected function isDetachment(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->node_type === NodeType::Detachment,
        );
    }

    protected function isDepartment(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->node_type === NodeType::Department,
        );
    }

    protected function hasDetail(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->relationLoaded('detail')
                ? $this->detail !== null
                : $this->detail()->exists(),
        );
    }

    protected function effectiveParent(): Attribute
    {
        return Attribute::make(
            get: function (): ?self {
                if ($this->node_type === NodeType::Detachment && $this->currentAttachment?->attachedTo) {
                    return $this->currentAttachment->attachedTo;
                }

                return $this->parent;
            },
        );
    }

    protected function breadcrumb(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $segments = $this->ancestorsAndSelf
                    ->sortBy($this->getDepthName())
                    ->map(fn (self $unit): string => $unit->abbreviation ?? $unit->name);

                return $segments->implode(' > ');
            },
        );
    }
}
