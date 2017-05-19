<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('to_user_id')->unsigned()->nullable()->change();

            $table->integer('channel_id')->unsigned()->nullable()->after('to_user_id');
            $table->foreign('channel_id')->references('id')->on('messenger_channels')->onDelete('cascade');

            $table->longText('message')->change();

            $table->string('attach_file', 512)->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['from_user_id']);
            $table->dropForeign(['to_user_id']);

            $table->integer('to_user_id')->unsigned()->change();

            $table->dropForeign(['channel_id']);
            $table->dropColumn('channel_id');

            $table->string('message')->change();

            $table->dropColumn('attach_file');
        });
    }
}
