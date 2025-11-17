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
        Schema::create('expense_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique(); // Auto-generated: ER-2025-001
            $table->string('title');
            $table->text('description')->nullable();

            // Montants
            $table->decimal('total_amount', 12, 3)->default(0);
            $table->decimal('total_ht', 12, 3)->default(0);
            $table->decimal('total_tva', 12, 3)->default(0);
            $table->string('currency', 3)->default('TND');

            // Workflow
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected, paid
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Comptabilité
            $table->string('accounting_code', 20)->nullable();
            $table->string('exported_to')->nullable(); // Sage, Ciel, etc.
            $table->timestamp('exported_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_reports');
    }
};
