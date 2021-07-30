<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchUser extends Model
{
    protected $table = 'branch_users';
	protected $fillable = [ 
        'user_id',
        'branch_id',
    ];
    
    public $timestamps = false;
}
