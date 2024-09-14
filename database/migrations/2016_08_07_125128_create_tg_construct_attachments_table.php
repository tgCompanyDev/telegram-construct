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
        Schema::create('tg_construct_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('original_name');
            $table->string('mime');
            $table->string('extension')->nullable();
            $table->bigInteger('size')->default(0);
            $table->integer('sort')->default(0);
            $table->text('path');
            $table->text('description')->nullable();
            $table->text('alt')->nullable();
            $table->text('hash')->nullable();
            $table->string('disk')->default('public');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('group')->nullable();

            $table->timestamps();
        });

        Schema::create('tg_construct_attachmentable', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tg_construct_attachmentable_type');
            $table->unsignedInteger('tg_construct_attachmentable_id');
            $table->unsignedInteger('tg_construct_attachment_id');
//
            $table->index(['tg_construct_attachmentable_type', 'tg_construct_attachmentable_id']);
//
            $table->foreign('tg_construct_attachment_id')
                ->references('id')
                ->on('tg_construct_attachments')
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
        Schema::drop('tg_construct_attachmentable');
        Schema::drop('tg_construct_attachments');
    }
};
