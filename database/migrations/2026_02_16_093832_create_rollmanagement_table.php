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
    Schema::create('rollmanagement', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('name')->nullable();           // e.g., "Admin"
        $table->text('identity_permission')->nullable();
        $table->string('createby')->nullable(); 
        $table->string('updateby')->nullable();  
        $table->string('createtime')->nullable(); 
        $table->string('updatetime')->nullable(); // Stores "add_user,edit_user"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rollmanagement');
    }
};
