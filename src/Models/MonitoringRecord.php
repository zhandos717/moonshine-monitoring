<?php

namespace Zhandos717\MoonshineMonitoring\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Zhandos717\MoonshineMonitoring\Database\Factories\MonitoringRecordFactory;

/**
 * @property string $instance_name
 * @property ?float $cpu
 * @property ?float $memory
 * @property ?float $disk
 * @property ?int $cpu_cores
 * @property ?int $memory_total_bytes
 * @property ?int $disk_total_bytes
 * @property Carbon $created_at
 */
class MonitoringRecord extends Model
{
    /** @use HasFactory<MonitoringRecordFactory> */
    use HasFactory;
    use MassPrunable;

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

    public static function purgeCutoff(): ?Carbon
    {
        $period = config('monitoring.purge_before');

        return $period ? now()->modify((string) $period) : null;
    }

    /**
     * Удаляет записи старше monitoring.purge_before; null/false в конфиге отключает очистку.
     */
    public static function purgeOld(): int
    {
        $cutoff = static::purgeCutoff();

        return $cutoff ? static::query()->where('created_at', '<', $cutoff)->delete() : 0;
    }

    /**
     * @return Builder<static>
     *
     * Для php artisan model:prune --model="Zhandos717\MoonshineMonitoring\Models\MonitoringRecord".
     */
    public function prunable(): Builder
    {
        $cutoff = static::purgeCutoff();

        return $cutoff ? static::query()->where('created_at', '<', $cutoff) : static::query()->whereRaw('1 = 0');
    }

    protected static function newFactory(): MonitoringRecordFactory
    {
        return MonitoringRecordFactory::new();
    }
}
