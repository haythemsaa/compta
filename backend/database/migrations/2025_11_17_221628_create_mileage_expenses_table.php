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
        Schema::create('mileage_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->string('start_location');
            $table->string('end_location');
            $table->decimal('distance_km', 10, 2);
            $table->boolean('round_trip')->default(false);

            // Calculs
            $table->integer('fiscal_power'); // CV stocké au moment du calcul
            $table->decimal('rate_per_km', 10, 3); // Barème applicable
            $table->decimal('total_amount', 12, 3);
            $table->string('currency', 3)->default('TND');

            // Justificatifs
            $table->text('description')->nullable();
            $table->string('purpose')->nullable(); // Motif du déplacement

            // Google Maps API
            $table->json('route_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mileage_expenses');
    }
};
