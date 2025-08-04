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
        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('phone')->after('email')->nullable();
            $table->tinyInteger('status')->default(1)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->dropColumn(['last_name', 'phone', 'status']);
        });
    }
};
