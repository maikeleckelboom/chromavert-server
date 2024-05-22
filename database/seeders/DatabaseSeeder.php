<?php

namespace Database\Seeders;

use App\Models\User;

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

        $this->createSuperAdminUser();
        $this->createTestUser();
    }

    public function createSuperAdminUser(): void
    {
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        $user = UserFactory::new()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole($role);
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
