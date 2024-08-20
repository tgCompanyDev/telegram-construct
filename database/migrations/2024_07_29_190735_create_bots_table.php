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
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->uuid('secret_token')->unique();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->string('first_name')->nullable();
            $table->string('user_name')->nullable();
            $table->string('webhook')->nullable();
            $table->jsonb('permissions')->nullable();
            $table->mediumText('token')->unique();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
