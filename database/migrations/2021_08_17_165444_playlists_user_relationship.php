<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlaylistsUserRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playlists', function ($table) {
            $table->bigInteger('owner_id')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('normal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()

    {
        Schema::table('playlists', function ($table) {

            $table->dropForeign('playlists_owner_id_foreign');
            $table->dropColumn('owner_id');
        });
    }
}
