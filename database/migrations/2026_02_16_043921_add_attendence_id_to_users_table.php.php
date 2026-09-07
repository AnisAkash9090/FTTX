<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Adding the column after the 'id' column
        $table->string('attendece_id')->nullable()->after('id'); 
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // This allows you to undo the migration if needed
        $table->dropColumn('attendece_id');
    });
}
};
