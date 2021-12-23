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
        //
        Schema::create('travel_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path', 255)->default(true);
            $table->foreignId('th_id')->references('id')->on('travel_histories')->onDelete('cascade');
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
        //
        Schema::dropIfExists('travel_images');
        // Schema::dropForeign('travel_images_th_id_foreign');
        // Schema::dropIndex('travel_images_th_id_index');
        // Schema::dropColumn('th_id');
    }
}
