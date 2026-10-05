<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_records', function (Blueprint $table) {
            // Страница и уведомления всегда фильтруют по серверу и периоду
            $table->index(['instance_name', 'created_at'], 'monitoring_records_instance_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_records', function (Blueprint $table) {
            $table->dropIndex('monitoring_records_instance_created_at_index');
        });
    }
};
