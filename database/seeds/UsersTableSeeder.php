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
        $adminRole->display_name = 'システム管理者ロール';
        $adminRole->save();

        $userRole = new \App\Role();
        $userRole->name = 'user';
        $userRole->display_name = '一般ユーザ';
        $userRole->save();

        /////
        $adminPerm = new \App\Permission();
        $adminPerm->name = 'system-admin';
        $adminPerm->display_name = 'システム管理権限';
        $adminPerm->save();

        $abUserPerm = new \App\Permission();
        $abUserPerm->name = 'addressbook-user';
        $abUserPerm->display_name = 'Web電話帳 ユーザ権限';
        $abUserPerm->save();

        $abAdminPerm = new \App\Permission();
        $abAdminPerm->name = 'addressbook-admin';
        $abAdminPerm->display_name = 'Web電話帳 管理権限';
        $abAdminPerm->save();

        $cdrUserPerm = new \App\Permission();
        $cdrUserPerm->name = 'cdr-user';
        $cdrUserPerm->display_name = '発着信履歴 ユーザ権限';
        $cdrUserPerm->save();

        $cdrSuperUserPerm = new \App\Permission();
        $cdrSuperUserPerm->name = 'cdr-superuser';
        $cdrSuperUserPerm->display_name = '発着信履歴 全表示権限';
        $cdrSuperUserPerm->save();

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
        $adminRole->attachPermission($cdrSuperUserPerm);

        // 一般ユーザロール
        $userRole->attachPermission($abUserPerm);
        $userRole->attachPermission($cdrUserPerm);

        $user = \App\User::where('username', '=', 'admin')->first();

        $user->attachRole($adminRole);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}
