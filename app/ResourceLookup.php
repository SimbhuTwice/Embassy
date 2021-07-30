<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceLookup extends Model
{
    public $table = "resource_lookup";

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
        'consent_month',
        'consent_date',
        'consent_no',
        'consent_validity',
        'resource_item_value', 
        'created_by',
        'updated_by', 
        'created_at', 
        'updated_at'
    ];
}
