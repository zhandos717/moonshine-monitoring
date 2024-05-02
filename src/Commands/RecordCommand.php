<?php

namespace Zhandos717\MoonshineMonitoring\Commands;


use Exception;
use Illuminate\Console\Command;
use Zhandos717\MoonshineMonitoring\Actions\RecordUsage;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;

class RecordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moonshine-monitoring:record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record resources usages';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     *
     */
    public function handle()
    {
        app(RecordUsage::class)->record([
            'cpu'    => Monitoring::cpu()->getUsage(),
            'memory' => Monitoring::memory()->getUsage(),
            'disk'   => Monitoring::disk()->getUsage(),
        ]);
    }
}