<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailHooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_hooks', function (Blueprint $table) {
            $table->id();
            $table->string('title',150);
            $table->string('slug',150)->unique();
            $table->text('description');
            $table->boolean('status')->default(1)->comment("1=active, 0=in active");
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
        Schema::dropIfExists('email_hooks');
    }
}
