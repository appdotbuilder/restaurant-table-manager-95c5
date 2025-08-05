<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reservation
 *
 * @property int $id
 * @property string $customer_name
 * @property string $customer_phone
 * @property int $pax
 * @property \Illuminate\Support\Carbon $reservation_time
 * @property int $assigned_table_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Table $assignedTable
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereAssignedTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation wherePax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereReservationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'pax',
        'reservation_time',
        'assigned_table_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pax' => 'integer',
        'reservation_time' => 'datetime',
        'assigned_table_id' => 'integer',
    ];

    /**
     * Get the table assigned to this reservation.
     */
    public function assignedTable(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'assigned_table_id');
    }
}