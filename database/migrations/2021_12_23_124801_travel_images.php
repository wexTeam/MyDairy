<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TravelImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path', 255)->default(true);
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
            $table->dropIfExists('travel_images');
            });
    }
}
