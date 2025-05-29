<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_create_file_storage_table.php
public function up()
{
    Schema::create('file_storage', function (Blueprint $table) {
        $table->id();
        $table->string('filename');
        $table->string('filetype');
        $table->integer('filesize');
        $table->binary('filedata'); // This will be stored as VARBINARY in MSSQL
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_storage');
    }
};
