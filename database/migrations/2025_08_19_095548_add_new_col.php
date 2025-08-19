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
        Schema::table('client_reviews', function (Blueprint $table) {
            $table->enum('title', ['Mr', 'Mrs'])->default('Mr')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_reviews', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
