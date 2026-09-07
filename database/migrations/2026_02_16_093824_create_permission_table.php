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
    Schema::create('permission', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('attendence_id')->unique(); // Links to your user
        $table->text('access')->nullable();
        $table->text('update_info')->nullable(); 
        $table->text('create_by')->nullable();
        $table->text(column: 'createdate')->timestamps(); ;         // Stores "1,2,5"
        $table->timestamps();                       // created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission');
    }
};
