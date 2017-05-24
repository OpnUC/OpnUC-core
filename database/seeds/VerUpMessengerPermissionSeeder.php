<?php

use Illuminate\Database\Seeder;

class VerUpMessengerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Messengerのパーミッションを追加

        $mesUserPerm = new \App\Permission();
        $mesUserPerm->name = 'messenger-user';
        $mesUserPerm->display_name = 'Messenger 利用者';
        $mesUserPerm->save();

        $adminRole = \App\Role::where('name', 'admin')->first();
        $operatorRole = \App\Role::where('name', 'operator')->first();

        $adminRole->attachPermission($mesUserPerm);
        $operatorRole->attachPermission($mesUserPerm);

    }
}
