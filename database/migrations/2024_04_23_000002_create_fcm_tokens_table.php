<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('device_token')->nullable();
            $table->text('fcm_token')->nullable();
            $table->string('language')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'device_token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fcm_tokens');
    }
}; 