<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['content' => 'Admin', 'slug' => 'admin'],
            ['content' => 'Passager', 'slug' => 'passager'],
            ['content' => 'Conducteur', 'slug' => 'conducteur'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['content' => $roleData['content']],
                ['slug' => $roleData['slug']]
            );
        }
    }
}
