<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'role_name' => 'Admin',
                'permission' => '[{admin:true}]'
            ],
            [
                'id' => 2,
                'role_name' => 'Manager',
                'permission' => '[{manage:true}]'
            ]
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
        
        DB::table('role_user')->insert([
            'role_id' => 1,
            'user_id' => 1
        ]);
    }
}
