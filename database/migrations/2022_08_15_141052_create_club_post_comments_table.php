<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubPostCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_post_comments', function (Blueprint $table) {
            $table->id();
            // $table->foreignUuid('club_post_id')->constrained()->onDelete('cascade');
            // $table->uuid('club_post_id');
            // $table->foreign('club_post_id')->references('id')->on('club_posts')->onDelete('cascade');
            $table->uuid('club_post_id');
            $table->foreign('club_post_id')->references('id')->on('club_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->longText('message');
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
        Schema::dropIfExists('club_post_comments');
    }
}
