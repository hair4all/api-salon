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
        'branch_code',
        'image',
        'address',
        'address_id',
        'cash',
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
