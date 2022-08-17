<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostUserCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_user_comments', function (Blueprint $table) {
            $table->id();
            $table->longtext('message');
            $table->uuid('post_user_id');
            $table->foreign('post_user_id')->references('id')->on('post_users')->onDelete('cascade');
            // $table->foreignUuid('post_user_id')->constrained()->onDelete('cascade');
            // $table->uuid('post_user_id');
            // $table->foreign('post_user_id')->references('id')->on('post_users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('post_user_comments');
    }
}
