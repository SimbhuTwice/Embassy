<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchDeviceLocation extends Model
{
    protected $table = 'branch_device_location';
	protected $fillable = [ 
        'company_id',
        'branch_id',
        'device_type',
        'device_name',
        'device_location',
        'chart_type',
        'show_in_header',
        'plot_min',
        'plot_max',
        'plot_bands',
        'img_src',
        'device_uom',
        'device_description',
        'device_id',
        'show_in_trends',
        'trends_calculation',
        'sort_order',
        'chart_nos',
        'chart_nos_color',
        'is_multi_select',
        'header_bg_color',
        'is_clubbed',
        'show_count_value',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
