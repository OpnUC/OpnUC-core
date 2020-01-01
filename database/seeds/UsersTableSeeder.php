<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(env('APP_ENV') != 'testing'){
            if (!$this->command->confirm('Do you wish to refresh migration before seeding, it will clear users/permission/roles ?')) {
                exit;
            }
        }

        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Permission::truncate();
        Role::truncate();
        DB::table(config('permission.table_names.model_has_roles'))->truncate();
        DB::table(config('permission.table_names.model_has_permissions'))->truncate();
        DB::table(config('permission.table_names.role_has_permissions'))->truncate();

        /////
        $adminRole = Role::findOrCreate('admin');
        $adminRole->display_name = 'システム管理者ロール';
        $adminRole->save();

        $userRole = Role::findOrCreate('user');
        $userRole->display_name = '一般ユーザ';
        $userRole->save();

        /////
        $adminPerm = Permission::findOrCreate('system-admin');
        $adminPerm->display_name = 'システム管理権限';
        $adminPerm->save();

        $abUserPerm = Permission::findOrCreate('addressbook-user');
        $abUserPerm->display_name = 'Web電話帳 ユーザ権限';
        $abUserPerm->save();

        $abAdminPerm = Permission::findOrCreate('addressbook-admin');
        $abAdminPerm->display_name = 'Web電話帳 管理権限';
        $abAdminPerm->save();

        $cdrUserPerm = Permission::findOrCreate('cdr-user');
        $cdrUserPerm->display_name = '発着信履歴 ユーザ権限';
        $cdrUserPerm->save();

        $cdrSuperUserPerm = Permission::findOrCreate('cdr-superuser');
        $cdrSuperUserPerm->display_name = '発着信履歴 全表示権限';
        $cdrSuperUserPerm->save();

        /////
        User::truncate();

        DB::table('users')->insert([
            'display_name' => '管理者',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminadmin'),
        ]);

        // 管理ロール
        $adminRole->syncPermissions([
            $adminPerm,
            $abUserPerm,
            $abAdminPerm,
            $cdrUserPerm,
            $cdrSuperUserPerm
        ]);

        // 一般ユーザロール
        $userRole->syncPermissions([
            $abUserPerm,
            $cdrUserPerm
        ]);

        $user = \App\User::where('username', '=', 'admin')->first();

        $user->syncRoles($adminRole);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}
