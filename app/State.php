<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $table = "states";

    public $fillable = [
        'country_id',
        'state_name',
        'state_code',
        'created_by', 
        'updated_by', 
        'is_active', 
        'created_at', 
        'updated_at'
    ];
}
