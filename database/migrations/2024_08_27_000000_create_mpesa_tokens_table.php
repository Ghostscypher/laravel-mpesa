<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mpesa_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('app_id')->index(); // Hashed consumer_key + secret
            $table->string('environment')->default('sandbox');

            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mpesa_tokens');
    }
};
