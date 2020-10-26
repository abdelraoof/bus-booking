<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->foreign('line_id')
                ->references('id')->on('lines')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('city_slug')
                ->references('slug')->on('cities')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreign('line_id')
                ->references('id')->on('lines')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bus_id')
                ->references('id')->on('buses')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('captain_id')
                ->references('id')->on('captains')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('trip_id')
                ->references('id')->on('trips')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('station_id')
                ->references('id')->on('stations')
                ->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropForeign(['line_id']);
            $table->dropForeign(['city_slug']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['line_id']);
            $table->dropForeign(['bus_id']);
            $table->dropForeign(['captain_id']);
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['trip_id']);
            $table->dropForeign(['station_id']);
        });
    }
}
