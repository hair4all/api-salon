<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //
    protected $table = 'branches';
    protected $fillable = [
        'id',
        'name',
        'image',
        'address',
        'address_id',
        'phone',
        'email',
        'status',
        'manager_id',
        'is_deleted',
    ];

    public function manager()
    {
        return $this->belongsTo(Member::class, 'manager_id');
    }
}
