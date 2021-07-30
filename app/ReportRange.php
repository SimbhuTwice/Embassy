<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportRange extends Model
{
    public $timestamps = false;
    protected $table = 'report_ranges';
	protected $fillable = [
        'company_id',
        'branch_id',
        'range_name',
        'interval_name',
        'interval_value',
        'is_active',
        'sort_order'
    ];
}
