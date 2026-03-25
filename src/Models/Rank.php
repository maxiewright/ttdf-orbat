<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rank_grade_id
 * @property int $formation_id
 * @property string $title
 * @property string $abbreviation
 * @property string|null $insignia_path
 */
class Rank extends Model
{
    protected $guarded = [];

    public function rankGrade(): BelongsTo
    {
        return $this->belongsTo(RankGrade::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }
}
