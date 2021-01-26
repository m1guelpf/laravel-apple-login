<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppleIdColumnToUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('apple_id')->nullable();
        });
    }

    public function down()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->dropColumn('apple_id');
        });
    }
}
