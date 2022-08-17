<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubPostImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_post_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            // $table->foreignId('club_post_id')->constrained()->onDelete('cascade');
            $table->uuid('club_post_id');
            $table->foreign('club_post_id')->references('id')->on('club_posts')->onDelete('cascade');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('club_post_images');
    }
}
