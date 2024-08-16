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
        Schema::create('users_tg_settings', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->unsignedBigInteger('last_tg_message_id')->nullable();
            $table->unsignedBigInteger('phone')->nullable();
            $table->unsignedBigInteger('tg_user_id')->unique()->nullable();
            $table->string('tg_user_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_tg_settings');
    }

};
