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

        DB::table('permissions')->truncate();
        DB::table('permission_role')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_user')->truncate();

        /////
        $adminRole = new \App\Role();
        $adminRole->name = 'admin';
        $adminRole->display_name = '管理者ロール';
        $adminRole->save();

        $operatorRole = new \App\Role();
        $operatorRole->name = 'operator';
        $operatorRole->display_name = '担当者ロール';
        $operatorRole->save();

        /////
        $adminPerm = new \App\Permission();
        $adminPerm->name = 'system-admin';
        $adminPerm->display_name = 'システム管理';
        $adminPerm->save();

        $abUserPerm = new \App\Permission();
        $abUserPerm->name = 'addressbook-user';
        $abUserPerm->display_name = 'Web電話帳 表示';
        $abUserPerm->save();

        $abAdminPerm = new \App\Permission();
        $abAdminPerm->name = 'addressbook-admin';
        $abAdminPerm->display_name = 'Web電話帳 管理';
        $abAdminPerm->save();

        $cdrUserPerm = new \App\Permission();
        $cdrUserPerm->name = 'cdr-user';
        $cdrUserPerm->display_name = '発着信履歴 表示';
        $cdrUserPerm->save();
        /////

        DB::table('users')->truncate();

        DB::table('users')->insert([
            'display_name' => '管理者',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminadmin'),
        ]);

        // 管理ロール
        $adminRole->attachPermission($adminRole);
        $adminRole->attachPermission($abUserPerm);
        $adminRole->attachPermission($abAdminPerm);
        $adminRole->attachPermission($cdrUserPerm);

        // オペレータロール
        $operatorRole->attachPermission($abUserPerm);
        $operatorRole->attachPermission($abAdminPerm);
        $operatorRole->attachPermission($cdrUserPerm);

        $user = \App\User::where('username', '=', 'admin')->first();

        $user->attachRole($adminRole);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}
