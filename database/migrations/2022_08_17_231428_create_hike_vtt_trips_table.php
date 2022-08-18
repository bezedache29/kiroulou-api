<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHikeVttTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hike_vtt_trips', function (Blueprint $table) {
            $table->id();
            $table->string('distance');
            $table->string('height_difference')->nullable();
            $table->string('difficulty');
            $table->string('supplies')->nullable();
            $table->foreignId('hike_vtt_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hike_vtt_trips');
    }
}
