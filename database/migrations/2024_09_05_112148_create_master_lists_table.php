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
        Schema::create('master_lists', function (Blueprint $table) {
            $table->id("master_list_id");
            $table->string('name');
            $table->unsignedBigInteger('facilitator_id');
            $table->unsignedBigInteger('event_id');
            $table->timestamps();


            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('facilitator_id')->references('user_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_lists');
    }
};
