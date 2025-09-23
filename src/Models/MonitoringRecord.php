<?php

namespace Zhandos717\MoonshineMonitoring\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Zhandos717\MoonshineMonitoring\Database\Factories\MonitoringRecordFactory;

class MonitoringRecord extends Model
{
    use HasFactory;

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

    protected static function newFactory()
    {
        return MonitoringRecordFactory::new();
    }
}