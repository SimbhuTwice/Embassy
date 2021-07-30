<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleMaster extends Model
{
    protected $table = 'role_master';
	protected $fillable = [ 
        'role_name',
        'is_admin',
        'is_branch_admin',
        'description',
        'is_active',
    ];
}
