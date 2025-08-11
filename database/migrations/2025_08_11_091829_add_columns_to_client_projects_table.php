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
        Schema::table('client_projects', function (Blueprint $table) {
            $table->string('domain_expiry_date')->nullable()->after('end_date');
            $table->string('hosting_expiry_date')->nullable()->after('domain_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_projects', function (Blueprint $table) {
            $table->dropColumn(['domain_expiry_date', 'hosting_expiry_date']); 
        });
    }
};
