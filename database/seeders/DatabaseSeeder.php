<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('app.env') === 'production') {
            exit('I just stopped you getting fired. Love, DatabaseSeeder');
        }

        $this->createAdminUser();
        $this->createTestUser();
    }

    public function createAdminUser(): User
    {
        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'access-dashboard']);

        $role->givePermissionTo($permission);

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'root@user.com',
            'password' => bcrypt('password'),
        ]);

        return $user->assignRole('admin');
    }

    /**
     * @return void
     */
    public function createTestUser(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
