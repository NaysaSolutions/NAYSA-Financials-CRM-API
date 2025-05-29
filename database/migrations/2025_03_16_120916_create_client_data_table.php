<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientDataTable extends Migration
{
    public function up()
    {
        Schema::create('client_data', function (Blueprint $table) {
            $table->id();
            $table->json('data'); // JSON column
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_data');
    }
}
