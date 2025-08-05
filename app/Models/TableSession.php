<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\TableSession
 *
 * @property int $id
 * @property int $table_id
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property int $pax
 * @property string|null $customer_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Table $table
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession wherePax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableSession whereUpdatedAt($value)
 * @method static \Database\Factories\TableSessionFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class TableSession extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sessions_table';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'table_id',
        'start_time',
        'end_time',
        'pax',
        'customer_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'table_id' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'pax' => 'integer',
    ];

    /**
     * Get the table that this session belongs to.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}