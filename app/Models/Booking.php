<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $table = 'bookings';
    protected $fillable = [
        'id',
        'client_id',
        'booking_date',
        'notes',
        'status',
        'is_deleted',
    ];
}
