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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['food', 'clothes', 'money', 'other']);
            $table->integer('quantity')->nullable();
            $table->string('location');
            $table->enum('status', ['available', 'claimed', 'completed'])->default('available');
            $table->boolean('is_urgent')->default(false);
            $table->decimal('monetary_value', 10, 2)->nullable(); // Added for money donations
            $table->date('expiry_date')->nullable(); // Added for perishable donations
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
