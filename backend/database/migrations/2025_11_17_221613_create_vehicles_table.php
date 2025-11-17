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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Propriétaire principal
            $table->string('name'); // Ex: "Renault Clio - XX TU 1234"
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('registration_number', 20)->nullable();
            $table->integer('fiscal_power')->nullable(); // Puissance fiscale (CV)
            $table->string('fuel_type')->nullable(); // essence, diesel, electric, hybrid
            $table->string('type')->default('personal'); // personal, company
            $table->json('documents')->nullable(); // carte grise, assurance
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
