<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySettingNumberRewriteDisplay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_number_rewrites', function (Blueprint $table) {
            $table->boolean('display_replacement')->after('replacement')->default(false)->comment('表示も変換するか');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('display_replacement', function (Blueprint $table) {
        });
    }
}
