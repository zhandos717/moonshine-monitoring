<?php

namespace Zhandos717\MoonshineMonitoring\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringRecord extends Model
{
    protected $fillable = [
        'instance_name',
        'cpu',
        'memory',
        'disk',
    ];

    protected $casts = [
        'cpu'    => 'float',
        'memory' => 'float',
        'disk'   => 'float',
    ];
}