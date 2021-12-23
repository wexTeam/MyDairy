<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadedImagesWhileTravelingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_images_while_travelings', function (Blueprint $table) {
            $table->id();
            $table->string('image_path', 255);
            $table->index('th_id');
            $table->foreignId('th_id')->references('id')->on('travel_histories')->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('uploaded_images_while_travelings');
        Schema::dropForeign('uploaded_images_while_travelings_th_id_foreign');
        Schema::dropIndex('uploaded_images_while_travelings_th_id_index');
        Schema::dropColumn('th_id');
    }
}
