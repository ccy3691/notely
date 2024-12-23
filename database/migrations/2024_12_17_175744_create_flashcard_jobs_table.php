<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flashcard_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->integer('total_requested');
            $table->integer('total_processed')->default(0);
            $table->string('status')->default('pending'); // Status: pending, in-progress, completed, failed
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcard_jobs');
    }
};
