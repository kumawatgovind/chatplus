<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cover_image', 255)->nullable()->after('profile_image');
            $table->text('bio')->nullable()->after('cover_image');
            $table->string('website', 255)->nullable()->after('bio');
            $table->date('dob')->nullable()->after('website');
            $table->string('janam_din')->nullable()->after('dob');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dob');
            $table->dropColumn('website');
            $table->dropColumn('bio');
            $table->dropColumn('cover_image');
        });
    }
}
