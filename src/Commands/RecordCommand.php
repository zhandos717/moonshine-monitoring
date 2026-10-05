<?php

namespace Zhandos717\MoonshineMonitoring\Commands;

use Illuminate\Console\Command;
use Zhandos717\MoonshineMonitoring\Actions\RecordUsage;
use Zhandos717\MoonshineMonitoring\Alerts\AlertManager;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class RecordCommand extends Command
{
    protected $signature = 'moonshine-monitoring:record';

    protected $description = 'Record resource usage, purge old records and send alerts';

    public function handle(RecordUsage $recorder, AlertManager $alerts): int
    {
        $record = $recorder->record();
        $this->info('Resource usage recorded');

        $purged = MonitoringRecord::purgeOld();
        if ($purged > 0) {
            $this->line("Purged {$purged} old records");
        }

        foreach ($alerts->check($record->instance_name) as $metric) {
            $this->warn("Alert sent: {$metric}");
        }

        return self::SUCCESS;
    }
}
