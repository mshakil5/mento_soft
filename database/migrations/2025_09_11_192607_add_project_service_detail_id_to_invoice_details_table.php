<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->unsignedBigInteger('project_service_detail_id')->after('client_project_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropColumn('project_service_detail_id');
        });
    }
};
