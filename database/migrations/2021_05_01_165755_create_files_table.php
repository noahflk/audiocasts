<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('audiobook_id', 32);
            $table->string('name');
            $table->string('path');
            $table->string('type');
            $table->integer('filesize');
            $table->integer('playtime');
            $table->integer('bitrate');
            $table->integer('mtime');
            $table->string('cover')->nullable();
            $table->timestamps();

            $table->foreign('audiobook_id')
                ->references('id')->on('audiobooks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
