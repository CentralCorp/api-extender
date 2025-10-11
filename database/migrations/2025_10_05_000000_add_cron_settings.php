<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apiextender_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaultSettings = [
            [
                'key' => 'cron_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable or disable the Cron API routes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'last_cron_execution',
                'value' => null,
                'type' => 'datetime',
                'description' => 'Timestamp of the last cron execution',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'cron_execution_count',
                'value' => '0',
                'type' => 'integer',
                'description' => 'Total number of cron executions',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('apiextender_settings')->insert($defaultSettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apiextender_settings');
    }
};