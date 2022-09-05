<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_push_notifications')->default(true);
            $table->boolean('is_email_notifications')->default(true);
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_club_admin')->default(false);
            $table->string('stripe_customer_id')->default(0)->nullable();
            $table->dateTime('last_connexion')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
