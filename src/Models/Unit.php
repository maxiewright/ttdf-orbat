<?php

namespace MaxieWright\TtdfOrbat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $formation_id
 * @property int|null $parent_id
 * @property string $node_type
 * @property string|null $service_branch
 * @property string $name
 * @property string|null $abbreviation
 * @property string|null $designation
 * @property string $status
 * @property int $sort_order
 */
class Unit extends Model
{
    protected $guarded = [];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
