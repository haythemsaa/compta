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
        Schema::create('expense_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            // Limites
            $table->decimal('max_amount', 12, 3)->nullable();
            $table->decimal('max_amount_per_day', 12, 3)->nullable();
            $table->decimal('max_amount_per_month', 12, 3)->nullable();

            // Approbation
            $table->boolean('requires_manager_approval')->default(true);
            $table->boolean('requires_accountant_approval')->default(false);
            $table->boolean('requires_daf_approval')->default(false);
            $table->decimal('auto_approve_below', 12, 3)->nullable();

            // Règles
            $table->json('rules')->nullable(); // Règles personnalisées
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_policies');
    }
};
