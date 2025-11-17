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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('role')->after('email')->default('employee'); // employee, manager, accountant, daf, admin
            $table->string('phone')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('role');
            $table->string('department')->nullable()->after('job_title');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn([
                'organization_id',
                'role',
                'phone',
                'job_title',
                'department',
                'is_active',
                'last_login_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
