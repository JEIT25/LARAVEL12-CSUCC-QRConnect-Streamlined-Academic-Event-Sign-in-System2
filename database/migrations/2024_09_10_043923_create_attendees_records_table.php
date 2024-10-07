<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendee_records', function (Blueprint $table) {
            $table->id("attendee_record_id");
            $table->foreignId('master_list_member_id')->constrained('master_list_members',"master_list_member_id")->onDelete('cascade')->onUpdate('cascade');  // Foreign key to users table
            $table->foreignId('event_id')->constrained('events',"event_id")->onDelete('cascade')->onUpdate('cascade');   // Foreign key to events table
            $table->dateTime('check_in')->nullable();   // Check-in datetime
            $table->dateTime('check_out')->nullable();  // Check-out datetime
            $table->dateTime('single_signin')->nullable();  // single sign in
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendee_records');
    }
};
