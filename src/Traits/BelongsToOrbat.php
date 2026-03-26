<?php

namespace MaxieWright\TtdfOrbat\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MaxieWright\TtdfOrbat\Models\Appointment;
use MaxieWright\TtdfOrbat\Models\Formation;
use MaxieWright\TtdfOrbat\Models\Rank;
use MaxieWright\TtdfOrbat\Models\Unit;

/**
 * Add to a consumer model (typically User) to link it to the ORBAT.
 *
 * Requires the consuming model's table to have nullable FK columns:
 *   - rank_id        → ranks.id
 *   - unit_id        → units.id
 *   - formation_id   → formations.id
 *   - appointment_id → appointments.id
 *
 * Publish the companion migration with:
 *   php artisan ttdf-orbat:install --with-user-fields
 */
trait BelongsToOrbat
{
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopeInUnit(Builder $query, int|Unit $unit): void
    {
        $query->where('unit_id', $unit instanceof Unit ? $unit->id : $unit);
    }

    public function scopeInFormation(Builder $query, int|Formation $formation): void
    {
        $query->where('formation_id', $formation instanceof Formation ? $formation->id : $formation);
    }

    public function scopeWithRank(Builder $query, int|Rank $rank): void
    {
        $query->where('rank_id', $rank instanceof Rank ? $rank->id : $rank);
    }

    public function scopeWithAppointment(Builder $query, int|Appointment $appointment): void
    {
        $query->where('appointment_id', $appointment instanceof Appointment ? $appointment->id : $appointment);
    }
}
