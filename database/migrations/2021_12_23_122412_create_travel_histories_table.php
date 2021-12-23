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
            $table->date('starting_date')->nullable(true);
            $table->date('ending_date')->nullable(true);
            $table->index('user_id');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('travel_histories');
        Schema::dropForeign('travel_histories_user_id_foreign');
        Schema::dropIndex('travel_histories_user_id_index');
        Schema::dropColumn('user_id');
    }
}
