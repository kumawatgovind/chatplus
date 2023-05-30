<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 200);
            $table->string('sub_title', 200)->nullable();
            $table->string('slug', 200);
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('banner', 200)->nullable();
            $table->string('meta_title', 200);
            $table->string('meta_keyword', 200);
            $table->text('meta_description');
            $table->string('position',200)->nullable()->default('left');
            $table->boolean('status');
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
        Schema::dropIfExists('pages');
    }
}
