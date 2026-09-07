<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oltdatatablepresent', function (Blueprint $table) {
        $table->id(); // Primary Key
        
        // Identity & Connection Info
        $table->string('sn')->nullable();
        $table->string('sys_sn')->nullable();
        $table->string('sys_mac')->nullable();
        $table->string('sys_port')->nullable();
        
        // Signal & Traffic Data
        $table->string('sys_tx')->nullable();
        $table->string('sys_rx')->nullable();
        $table->string('upload')->nullable();
        $table->string('download')->nullable();
        
        // Hardware Info
        $table->string('sys_vendor')->nullable();
        $table->string('sys_model')->nullable();
        $table->string('sys_device')->nullable();
        $table->string('sys_distance')->nullable();
        
        // Status & Timestamps
        $table->string('sys_timeTicks')->nullable();
        $table->string('sys_lastChange')->nullable();
        $table->string('sys_from')->nullable();
        $table->integer('sys_sts')->nullable();
        $table->text('reason')->nullable();
        
        // System Tracking
        $table->string('sys_gen_id')->nullable();
        $table->timestamp('sync_time')->nullable();
        
        $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oltdatatablepresent');
    }
};
