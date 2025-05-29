<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In the migration file
public function up()
{
    Schema::create('client_files', function (Blueprint $table) {
        $table->string('file_id')->primary(); // ID generated from stored procedure
        $table->string('client_code');
        $table->string('original_name');
        $table->string('stored_name');
        $table->string('path');
        $table->dateTime('upload_date');
        $table->date('signed_date')->nullable();
        $table->timestamps();
        
        $table->foreign('client_code')->references('client_code')->on('clients');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_files');
    }
};
