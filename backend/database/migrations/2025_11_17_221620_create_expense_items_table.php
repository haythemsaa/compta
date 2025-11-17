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
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->text('description')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('merchant_vat_number', 20)->nullable(); // Matricule fiscal

            // Montants
            $table->decimal('amount', 12, 3);
            $table->decimal('amount_ht', 12, 3)->nullable();
            $table->decimal('tva_rate', 5, 2)->nullable(); // 19, 13, 7, 0
            $table->decimal('tva_amount', 12, 3)->nullable();
            $table->string('currency', 3)->default('TND');
            $table->decimal('exchange_rate', 10, 6)->default(1); // Si devise étrangère
            $table->decimal('amount_in_default_currency', 12, 3)->nullable();

            // Invités (repas d'affaires)
            $table->integer('guest_count')->nullable();
            $table->json('guest_names')->nullable();

            // Données OCR
            $table->json('ocr_data')->nullable();
            $table->decimal('ocr_confidence', 5, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
