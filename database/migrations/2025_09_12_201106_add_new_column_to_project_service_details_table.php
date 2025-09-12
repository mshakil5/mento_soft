<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_service_details', function (Blueprint $table) {
            $table->date('service_renewal_date')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('project_service_details', function (Blueprint $table) {
            $table->dropColumn('service_renewal_date');
        });
    }
};
