<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchSettings extends Model
{
    protected $table = 'branch_settings';
	protected $fillable = [ 
        'company_id',
        'branch_id',
        'field',
        'value',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
