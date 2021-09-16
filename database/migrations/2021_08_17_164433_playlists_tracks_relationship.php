<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlaylistsTracksRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlist_track', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('playlist_id')->unsigned();
            $table->string('track_id');
            $table->foreign('playlist_id')->references('id')->on('playlists')
                ->onDelete('cascade');
            $table->foreign('track_id')->references('id')->on('tracks')
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
        Schema::dropIfExists('playlist_track');
    }
}
