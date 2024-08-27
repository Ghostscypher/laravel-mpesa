<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Stores mpesa callbacks in a table
        Schema::create('mpesa_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('app_id')->index(); // Hashed consumer_key + secret
            $table->string('environment')->default('sandbox');

            $table->string('reference_id')->nullable()->index();
            $table->string('callback_type');
            $table->string('endpoint');
            $table->string('method');
            $table->ipAddress('ip');
            $table->string('user_agent');
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_headers')->nullable();
            $table->json('response_body')->nullable();
            $table->integer('response_status')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mpesa_callbacks');
    }
};
