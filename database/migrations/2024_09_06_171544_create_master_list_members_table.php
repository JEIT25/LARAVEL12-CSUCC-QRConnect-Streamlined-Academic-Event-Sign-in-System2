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
        Schema::create('master_list_members', function (Blueprint $table) {
            $table->id("master_list_member_id");
            $table->unsignedBigInteger('master_list_id');
            $table->string('full_name');
            $table->string('unique_id');
            $table->timestamps();

            $table->foreign('master_list_id')->references('master_list_id')->on('master_lists')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_list_members');
    }
};
