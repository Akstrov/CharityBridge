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
        Schema::create('charity_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('organization_name');
            $table->string('organization_logo')->nullable();
            $table->text('mission_statement')->nullable();
            $table->text('description');
            $table->string('website')->nullable();
            $table->string('registration_number')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('category');
            $table->string('tax_id')->nullable();
            $table->text('areas_of_focus')->nullable();
            $table->year('year_established')->nullable();
            $table->string('social_media_links')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charity_details');
    }
};
