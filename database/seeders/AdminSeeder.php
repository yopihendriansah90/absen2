<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Buat roel 'super_admin' jika belum ada

        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
        );
        // Membuat user admin
        $user = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            ['name' => 'Admin', 'email' => 'admin@mail.com', 'password' => Hash::make('admin'), 'nip' => '1234567890', 'position' => 'Admin', 'phone' => '081234567890', 'address' => 'Jl. Admin', 'is_active' => true]
        );

        // Assign Role 'super_admin'
        // Pastikan Anda sudah menjalankan 'php artisan shield:install' sebelumnya
        $user->assignRole($role);

    }
}
