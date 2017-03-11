<?php

use Illuminate\Database\Seeder;

/**
 * デモ用のデータSeeder
 * Class DemoSeeder
 */
class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create('ja_JP');

        // 発着信履歴
        $cdrType = [10, 21, 22, 23];

        for ($i = 0; $i < 1000; $i++) {
            $cdrItem = new \App\Cdr();

            $cdrItem->type = $cdrType[array_rand($cdrType)];
            $cdrItem->sender = rand(200, 800);
            $cdrItem->destination = rand(200, 800);

            $baseTime = time() + rand();

            $cdrItem->duration = rand(10, 3600);
            $cdrItem->start_datetime = date('Y-m-d H:i:s', $baseTime);
            $cdrItem->end_datetime = date('Y-m-d H:i:s', $baseTime + $cdrItem->duration);

            $cdrItem->save();
        }

        // アドレス帳グループ
        $grpParent1 = new \App\AddressBookGroup();
        $grpParent1->type = 2;
        $grpParent1->parent_groupid = 0;
        $grpParent1->owner_userid = 0;
        $grpParent1->group_name = '共通 親テスト1';

        $grpParent1->save();

        $grpChild1_1 = new \App\AddressBookGroup();
        $grpChild1_1->type = 2;
        $grpChild1_1->parent_groupid = $grpParent1->id;
        $grpChild1_1->owner_userid = 0;
        $grpChild1_1->group_name = '共通 子テスト1';

        $grpChild1_1->save();

        $grpChild1_1_1 = new \App\AddressBookGroup();
        $grpChild1_1_1->type = 2;
        $grpChild1_1_1->parent_groupid = $grpChild1_1->id;
        $grpChild1_1_1->owner_userid = 0;
        $grpChild1_1_1->group_name = '共通 孫テスト1';

        $grpChild1_1_1->save();

        $grpParent2 = new \App\AddressBookGroup();
        $grpParent2->type = 2;
        $grpParent2->parent_groupid = 0;
        $grpParent2->owner_userid = 0;
        $grpParent2->group_name = '共通 親テスト2';

        $grpParent2->save();

        $abGroupID = [$grpChild1_1_1->id, $grpParent2->id];

        for ($i = 0; $i < 1000; $i++) {
            $abItem = new \App\AddressBook();

            $abItem->type = 2;
            $abItem->owner_userid = 0;
            $abItem->groupid = $abGroupID[array_rand($abGroupID)];
            $abItem->position = $faker->realText(10);
            $abItem->name_kana =  $faker->realText(10);
            $abItem->name = $faker->name();
            $abItem->tel1 = $faker->phoneNumber();
            $abItem->tel2 = $faker->phoneNumber();
            $abItem->tel3 = $faker->phoneNumber();
            $abItem->email = $faker->email();
            $abItem->comment = $faker->realText(20);
            $abItem->avatar_type = 2;
            $abItem->avatar_filename = '';

            $abItem->save();

        }

    }
}
