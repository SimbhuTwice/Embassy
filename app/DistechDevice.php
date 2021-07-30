<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistechDevice extends Model
{
    protected $table = 'compbranch_distech_1';
	protected $fillable = [ 
        'company_id',
        'branch_id',
        'distech_deviceip',
        'object_type',
        'object_name',
        'asn_value',
        'present_value',
        'device_location',
        'status_flag',
        'status_date',
        'status_time',
    ];
}
