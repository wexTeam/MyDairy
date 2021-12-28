<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_histories', function (Blueprint $table) {
            $table->id();
            $table->double('longitude', 15, 8)->default(true);
            $table->double('latitude', 15, 8)->default(true);
            $table->dateTime('starting_date')->nullable(true);
            $table->dateTime('ending_date')->nullable(true);
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
        Schema::table('travel_histories', function (Blueprint $table) {
                $table->dropIfExists('travel_histories');
            });
    }
}
