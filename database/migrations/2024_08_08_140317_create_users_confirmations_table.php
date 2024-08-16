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
        Schema::create('users_confirmations', function (Blueprint $table) {
            $table->id();
            $table->string('input')->nullable();
            $table->unsignedInteger('tg_user_id')->nullable();
            $table->foreign('tg_user_id')
                ->references('id')
                ->on('tg_users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_confirmations');
    }
};
