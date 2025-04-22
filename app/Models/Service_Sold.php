<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service_Sold extends Model
{
    //
    protected $table = 'service__solds';
    protected $fillable = [
        'id',
        'service_id',
        'client_id',
        'branch_id',
        'worker_id',
        'sold_date',
        'is_deleted',
    ];
}
