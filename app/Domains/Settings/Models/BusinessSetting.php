<?php

namespace App\Domains\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];
}
