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
        'cpu_cores',
        'memory_total_bytes',
        'disk_total_bytes',
    ];

    protected $casts = [
        'cpu' => 'float',
        'memory' => 'float',
        'disk' => 'float',
        'cpu_cores' => 'integer',
        'memory_total_bytes' => 'integer',
        'disk_total_bytes' => 'integer',
    ];

    protected static function newFactory()
    {
        return MonitoringRecordFactory::new();
    }
}