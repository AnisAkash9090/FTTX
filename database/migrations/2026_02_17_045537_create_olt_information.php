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
        Schema::create('olt_information', function (Blueprint $table) {
            $table->integer('id')->autoIncrement(); 
            $table->string('user_name', 255);
            $table->string('olt_name', 255)->nullable();
            $table->string('olt_ip', 255)->nullable();
            $table->string('olt_community', 255)->nullable();
            $table->string('useradmin', 255)->nullable();
            $table->string('pass', 255)->nullable();
            $table->string('olt_type', 255)->nullable();
            $table->string('sts', 255);
            $table->string('type', 255)->nullable();
            $table->string('olt_brand', 255)->nullable();
            $table->string('createinfo', 255)->nullable();
            $table->string('typeconnection', 200)->nullable();
            $table->integer('txrxcmd')->default(0);
            $table->integer('port')->default(0); // Fixed cut-off column
   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_information');
    }
};