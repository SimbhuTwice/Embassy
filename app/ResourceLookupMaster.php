<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceLookupMaster extends Model
{
    public $table = "resource_lookup_master";

    public $fillable = [
        'company_id',
        'branch_id',
        'resource_no',
        'resource_name',
        'resource_act_year',
        'resource_group_no',
        'resource_group',
        'resource_item',
        'resource_item_uom',
        'sort_order',
        'is_active',
        'created_by',
        'updated_by', 
        'created_at', 
        'updated_at'
    ];
}
