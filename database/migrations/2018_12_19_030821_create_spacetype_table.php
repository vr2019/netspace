<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpacetypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spacetype', function (Blueprint $table) {
            $table->increments('vr_SpaceId');
            $table->string('vr_SpaceType', 100);
            $table->string('vr_SpaceName', 300);
            $table->integer('vr_SpaceSize');
            $table->dateTime('vr_CreateTime');
            $table->dateTime('vr_UpdateTime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spacetype');
    }
}
