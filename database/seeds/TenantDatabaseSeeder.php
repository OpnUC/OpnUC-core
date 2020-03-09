<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /////
        $adminRole = Role::findOrCreate('admin');
        $adminRole->display_name = 'システム管理者ロール';
        $adminRole->save();

        $userRole = Role::findOrCreate('user');
        $userRole->display_name = '一般ユーザ';
        $userRole->save();

        $pbxLinkerRole = Role::findOrCreate('pbxlinker');
        $pbxLinkerRole->display_name = 'PBX Linkerロール';
        $pbxLinkerRole->save();

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

        $pbxLinkerPerm = Permission::findOrCreate('pbxlinker');
        $pbxLinkerPerm->display_name = 'PBX Linker権限';
        $pbxLinkerPerm->save();

        // 管理ロール
        $adminRole->syncPermissions([
            $adminPerm,
            $abUserPerm,
            $abAdminPerm,
            $cdrUserPerm,
            $cdrSuperUserPerm,
            $pbxLinkerPerm,
        ]);

        // 一般ユーザロール
        $userRole->syncPermissions([
            $abUserPerm,
            $cdrUserPerm,
            $pbxLinkerPerm,
        ]);

        // PBX Linkerロール
        $pbxLinkerRole->syncPermissions([
            $pbxLinkerPerm,
        ]);

    }
}
