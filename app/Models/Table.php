<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Table
 *
 * @property int $id
 * @property string $table_name
 * @property int $capacity
 * @property string $status
 * @property float $position_x
 * @property float $position_y
 * @property int|null $current_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TableSession|null $currentSession
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TableSession> $sessions
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Table newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Table newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Table query()
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereCurrentSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table wherePositionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table wherePositionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereUpdatedAt($value)
 * @method static \Database\Factories\TableFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Table extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'table_name',
        'capacity',
        'status',
        'position_x',
        'position_y',
        'current_session_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'position_x' => 'decimal:2',
        'position_y' => 'decimal:2',
        'current_session_id' => 'integer',
    ];

    /**
     * Get the current session for the table.
     */
    public function currentSession(): BelongsTo
    {
        return $this->belongsTo(TableSession::class, 'current_session_id');
    }

    /**
     * Get all sessions for this table.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(TableSession::class);
    }

    /**
     * Get all reservations for this table.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'assigned_table_id');
    }
}