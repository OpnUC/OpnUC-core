<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初期ユーザのSeed
        $this->call(UsersTableSeeder::class);

        // Messengerのパーミッションを追加
        $this->call(VerUpMessengerPermissionSeeder::class);
    }
}
