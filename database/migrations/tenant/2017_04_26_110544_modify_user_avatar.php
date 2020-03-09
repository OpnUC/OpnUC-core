<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUserAvatar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // アバタータイプ
            $table->integer('avatar_type')->unsigned()->default(1)->after('remember_token');
            // ローカルのアバターを使う場合、ファイル名
            $table->string('avatar_filename')->nullable()->after('avatar_type');
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
