<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        $adminRole = new \App\Role();
        $adminRole->name = 'admin';
        $adminRole->display_name = '管理者';
        $adminRole->save();

        $operatorRole = new \App\Role();
        $operatorRole->name = 'operator';
        $operatorRole->display_name = '担当者';
        $operatorRole->save();

        $editAddressBook = new \App\Permission();
        $editAddressBook->name = 'edit-addressbook';
        $editAddressBook->display_name = 'アドレス帳 編集者';
        $editAddressBook->save();

        DB::table('users')->truncate();

        DB::table('users')->insert([
            'display_name' => '管理者',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminadmin'),
        ]);

        $adminRole->attachPermission($editAddressBook);
        $operatorRole->attachPermission($editAddressBook);

        $user = \App\User::where('username', '=', 'admin')->first();

        $user->attachRole($adminRole);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}
