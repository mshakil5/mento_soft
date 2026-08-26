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
        Schema::table('project_service_details', function (Blueprint $table) {
            // Use decimal for money/percentages to avoid floating-point math issues.
            // 8 is total digits, 2 is decimal places (e.g., 999999.99)
            
            $table->decimal('vat_percent', 8, 2)->default(0)->after('amount');
            $table->decimal('vat_amount', 8, 2)->default(0)->after('vat_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_service_details', function (Blueprint $table) {
            $table->dropColumn(['vat_percent', 'vat_amount']);
        });
    }
};