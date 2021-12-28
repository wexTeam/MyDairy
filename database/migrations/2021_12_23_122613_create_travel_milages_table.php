<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelMilagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_milages', function (Blueprint $table) {
            $table->id();
            $table->double('longitude', 15, 8)->default(true);
            $table->double('latitude', 15, 8)->default(true);
            $table->foreignId('travel_history_id')
                ->constrained()
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
        Schema::table('travel_milages', function (Blueprint $table) {
                $table->dropIfExists('travel_milages');
            });
    }
}
