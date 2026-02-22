<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PingResult extends Model
{
    /**
     * Indicates if the model should be timestamped.
     * Only created_at is used; updated_at is not needed.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ping_target_id',
        'latency',
        'is_reachable',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latency' => 'float',
            'is_reachable' => 'boolean',
        ];
    }

    /**
     * Get the ping target that owns the result.
     */
    public function pingTarget(): BelongsTo
    {
        return $this->belongsTo(PingTarget::class);
    }
}
