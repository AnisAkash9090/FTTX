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
        Schema::create('olt_oid', function (Blueprint $table) {
          $table->id(); // Auto-incrementing ID

        $table->string('assign_for')->nullable();
        $table->string('port_names')->nullable();
        $table->string('tx_values')->nullable();
        $table->string('alterTX')->nullable();
        $table->string('rx_values')->nullable();
        $table->string('up_value')->nullable();
        $table->string('down_values')->nullable();
        $table->string('sts')->nullable();
        $table->string('lcv')->nullable();
        $table->string('sn_values')->nullable();
        $table->string('reason')->nullable();
        $table->string('onumod')->nullable();
        $table->string('mac_get')->nullable();
        $table->string('onu_dist')->nullable();
        $table->string('onuvendor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_oid');
    }
};
