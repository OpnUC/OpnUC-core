<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteCdrBuffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('cdr_buffers');
    }

    /**
     * Reverse the migrations.
     *
     * @todo MigrationのRollbackに失敗するため、手動でテーブルを作成
     * @return void
     */
    public function down()
    {

        Schema::create('cdr_buffers', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('report_datetime');
            $table->string('facility');
            $table->string('priority');
            $table->string('type');
            $table->string('message', 1024);
            $table->timestamps();
        });

    }
}
