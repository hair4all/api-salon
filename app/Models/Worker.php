<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    //
    protected $table = 'workers';
    protected $fillable = [
        'id',
        'member_id',
        'branch_id',
        'name',
        'phone',
        'email',
        'address',
        'position_id',
        'status',
        'salary',
        'is_deleted',
    ];
}
