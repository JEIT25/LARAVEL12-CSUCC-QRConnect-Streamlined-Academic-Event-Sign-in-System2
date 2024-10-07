<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id("event_id");
            $table->unsignedBigInteger('facilitator_id');
            $table->string('type');
            $table->string('subject')->nullable();
            $table->string('subject_code')->nullable();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('location');
            $table->string('profile_image')->nullable();
            $table->enum('semester', ["1st", "2nd"])->nullable();
            $table->string('school_year')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('facilitator_id')->references('user_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
