<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportedToField extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('imported_to', 255)->default('');
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('votes');
        });
    }
}
