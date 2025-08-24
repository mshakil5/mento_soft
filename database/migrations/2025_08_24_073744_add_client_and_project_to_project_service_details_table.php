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
            $table->foreignId('client_id')->nullable()->after('project_service_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('client_project_id')->nullable()->after('client_id')->constrained('client_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_service_details', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['client_project_id']);
            $table->dropColumn(['client_id', 'client_project_id']);
        });
    }
};
