<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Valibool\TelegramConstruct\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->jsonb('permissions')->nullable();
        });
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'permissions' => [
                "platform.index" => true,
                "platform.systems.roles" => true,
                "platform.systems.users" => true,
                "platform.users.bots.edit" => true,
                "platform.systems.attachment" => true
            ]
        ]);
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
//        Artisan::call('artisan db:seed —class=UserTableSeeder');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_tg_settings');
    }

};
