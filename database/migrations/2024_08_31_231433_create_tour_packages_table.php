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
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('duration_days');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_participants')->nullable();
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->json('itinerary');
            $table->json('pricing_tiers');
            $table->boolean('published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
