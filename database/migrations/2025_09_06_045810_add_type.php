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
            $table->tinyInteger('type')->default(1)->after('status'); // 1 = in house 2 = third party
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_service_details', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
