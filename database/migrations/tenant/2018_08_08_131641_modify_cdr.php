<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCdr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cdrs', function (Blueprint $table) {
            $table->string('sender_comment')->after('sender')->comment('発信元 備考');
            $table->string('destination_comment')->after('destination')->comment('着信先 備考');
        });

        // DataConvert
        $cdrs = App\Cdr::all();

        foreach ($cdrs as $cdr) {
            if (preg_match('/^([0-9\#\*]+)\((.*)\)$/', $cdr->sender, $senderResult)) {
                $cdr->sender = $senderResult[1];
                $cdr->sender_comment = $senderResult[2];
            }

            if (preg_match('/^([0-9\#\*]+)\((.*)\)$/', $cdr->destination, $destinationResult)) {
                $cdr->destination = $destinationResult[1];
                $cdr->destination_comment = $destinationResult[2];
            }

            $cdr->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // DataConvert
        $cdrs = App\Cdr::all();

        foreach ($cdrs as $cdr) {
            if ($cdr->sender_comment) {
                $cdr->sender = sprintf('%s(%s)', $cdr->sender, $cdr->sender_comment);
            }

            if ($cdr->destination_comment) {
                $cdr->destination = sprintf('%s(%s)', $cdr->destination, $cdr->destination_comment);
            }

            $cdr->save();
        }

        Schema::table('cdrs', function (Blueprint $table) {
            $table->dropColumn('sender_comment');
            $table->dropColumn('destination_comment');
        });

    }
}
