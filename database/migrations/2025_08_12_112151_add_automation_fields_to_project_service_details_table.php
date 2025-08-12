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
            $table->boolean('is_auto')->default(false)->after('note');
            $table->unsignedTinyInteger('cycle_type')->default(2)->comment('1 = Monthly, 2 = Yearly')->after('is_auto');
            $table->string('next_start_date')->nullable()->after('cycle_type');
            $table->string('next_end_date')->nullable()->after('next_start_date');
            $table->boolean('next_created')->default(false)->after('next_end_date');
            $table->boolean('bill_paid')->default(false)->after('next_created');
            $table->dateTime('last_auto_run')->nullable()->after('bill_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_service_details', function (Blueprint $table) {
            $table->dropColumn('is_auto');
            $table->dropColumn('cycle_type');
            $table->dropColumn('next_start_date');
            $table->dropColumn('next_end_date');
            $table->dropColumn('next_created');
            $table->dropColumn('bill_paid');
            $table->dropColumn('last_auto_run');
        });
    }
};
