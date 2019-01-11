<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpacefileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spacefile', function (Blueprint $table) {
            $table->increments('vr_FileId');
            $table->integer('vr_ParentId');
            $table->integer('vr_SpaceId');
            $table->integer('vr_UserId');
            $table->boolean('vr_IsForder');
            $table->string('vr_FileName', 300);
            $table->string('vr_ShowName', 300);
            $table->string('vr_FileType', 100);
            $table->string('vr_FileSize', 100);
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
        Schema::dropIfExists('spacefile');
    }
}
