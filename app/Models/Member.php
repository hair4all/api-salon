<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //
    protected $table = 'members';
    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'is_deleted',
    ];
}
