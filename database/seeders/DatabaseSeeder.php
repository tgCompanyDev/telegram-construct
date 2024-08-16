<?php

namespace seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Valibool\TelegramConstruct\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }

    protected function createAdmin()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
//        Artisan::call('orchid:admin admin admin@admin.com password');
        Artisan::call('artisan db:seed —class=UserTableSeeder');
    }
}