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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->mediumText('text')->nullable();
            $table->string('type')->default('message');
            $table->string('name')->nullable();
            $table->boolean('first_message')->default(false);
            $table->string('wait_input')->nullable();
            $table->boolean('need_confirmation')->default(false);
            $table->unsignedInteger('bot_id')->nullable();
            $table->foreign('bot_id')
                ->references('id')
                ->on('bots')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedInteger('next_message_id')->nullable();
            $table->foreign('next_message_id')
                ->references('id')
                ->on('messages')
                ->onUpdate('cascade')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
