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
    Schema::create('userlists', function (Blueprint $table) {
        $table->id('idU'); // Your primary key
        $table->string('attendece_id')->nullable();
        $table->string('img')->nullable(); // Stores the image path
        $table->string('name');
        $table->string('email')->unique();
        $table->text('address')->nullable();
        $table->integer('sts')->default(1); // Status (e.g., 1 for active)
        $table->string('createinfo')->nullable();
        $table->timestamps(); // Adds created_at and updated_at
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userlists');
    }
};
