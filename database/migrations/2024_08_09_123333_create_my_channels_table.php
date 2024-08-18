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
        Schema::create('my_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel_tg_id');
            $table->string('title');
            $table->string('username')->nullable();
            $table->unsignedInteger('bot_id');
            $table->foreign('bot_id')
                ->references('id')
                ->on('bots')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_channels');
    }
};
