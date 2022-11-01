<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /** ToDo: Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different showrooms

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('duration');
            $table->timestamps();
        });

        Schema::create('show_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('capacity');
            $table->timestamps();
        });

        Schema::create('show_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });

        Schema::create('show_times', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('show_room_id');
            $table->dateTime('start_time');
            $table->unsignedInteger('available_seats');
            $table->timestamps();
            $table->foreign('show_room_id')->references('id')->on('show_rooms')->onDelete('cascade');
            $table->foreign('show_date_id')->references('id')->on('show_dates')->onDelete('cascade');
            $table->unique(['cinema_id', 'start_time', 'show_date_id'], 'show_id');
        });

        Schema::create('show_times_seats_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->float('price');
            $table->float('premium_percentage');
            $table->timestamps();
        });

        Schema::create('show_times_seats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('total_seats');
            $table->unsignedInteger('show_times_seats_types_id')->nullable();
            $table->unsignedInteger('show_times_id')->nullable();
            $table->foreign('show_times_seats_types_id')->references('id')->on('show_times_seats_types')->onDelete('set null');
            $table->foreign('show_times_id')->references('id')->on('show_times')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('show_times_seats_id');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('show_id')->nullable();
            $table->foreign('show_times_seats_id')->references('id')->on('show_times_seats')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('show_id')->references('id')->on('show_times')->onDelete('set null');

            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('show_times')->onDelete('cascade');
            $table->integer('show_date_id')->unsigned();
            $table->foreign('show_date_id')->references('id')->on('show_dates')->onDelete('cascade');
            $table->integer('total_tickets');
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
    }
}
