<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToMonitoringRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitoring_records', function (Blueprint $table) {
            $table->integer('cpu_cores')->nullable()->after('cpu');
            $table->bigInteger('memory_total_bytes')->nullable()->after('memory');
            $table->bigInteger('disk_total_bytes')->nullable()->after('disk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitoring_records', function (Blueprint $table) {
            $table->dropColumn(['cpu_cores', 'memory_total_bytes', 'disk_total_bytes']);
        });
    }
}