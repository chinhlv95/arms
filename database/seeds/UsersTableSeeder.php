<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'fullname' => 'System Admin',
            'username' => 'admin',
            'password' => bcrypt('12345678'),
            'email' => 'systemadmin@gmail.com',
            'calling_code' => '+84',
            'id_resource' => 1,
            'permission' => '[{admin:true}]'
        ]);
    }
}
