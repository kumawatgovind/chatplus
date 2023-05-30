<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_sync', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('code', 100)->nullable();
            $table->string('cid', 200)->nullable();
            $table->string('name', 200)->nullable();
            $table->string('number', 200)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_sync');
    }
}
