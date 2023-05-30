<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileFieldUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('device_id')->nullable()->after('status');            
            $table->string('device_type', 255)->nullable()->after('device_id');            
            $table->text('api_token')->nullable()->after('device_type');            
            $table->text('firebase_email')->nullable()->after('api_token');            
            $table->text('firebase_password')->nullable()->after('firebase_email');            
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
            $table->dropColumn('firebase_password');
            $table->dropColumn('firebase_email');
            $table->dropColumn('api_token');
            $table->dropColumn('device_type');
            $table->dropColumn('device_id');
        });
    }
}
