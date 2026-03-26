<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Pivot model linking an audience-targetable consumer model to an ORBAT entity
 * (Formation, Unit, or RankGrade).
 *
 * @property int $id
 * @property string $auditable_type
 * @property int $auditable_id
 * @property string $targetable_type
 * @property int $targetable_id
 */
class OrbatAudience extends Model
{
    protected $guarded = [];

    /** The consumer model that has this audience target (e.g. a Notice). */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /** The ORBAT entity being targeted (Formation, Unit, or RankGrade). */
    public function targetable(): MorphTo
    {
        return $this->morphTo();
    }
}
