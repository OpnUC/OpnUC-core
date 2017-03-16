<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAddressbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('address_books', function (Blueprint $table) {
            // 個人電話帳の場合の所有者ID
            $table->integer('owner_userid')->default(0)->change();
            // 役職
            $table->string('position')->nullable()->change();
            // 電話番号
            $table->string('tel1')->nullable()->change();
            $table->string('tel2')->nullable()->change();
            $table->string('tel3')->nullable()->change();
            // メールアドレス
            $table->string('email')->nullable()->change();
            // 備考
            $table->string('comment')->nullable()->change();
            // アバタータイプ
            $table->integer('avatar_type')->unsigned()->default(1)->change();
            // ローカルのアバターを使う場合、ファイル名
            $table->string('avatar_filename')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
