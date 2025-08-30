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
            $table->string('contact_no')->nullable()->after('user_type');
            $table->string('joining_date')->nullable()->after('contact_no');
            $table->string('em_contact_person')->nullable()->after('joining_date');
            $table->string('em_contact_no')->nullable()->after('em_contact_person');
            $table->string('nid')->nullable()->after('em_contact_no');
            $table->Text('address')->nullable()->after('nid');
            $table->string('salary')->nullable()->after('address');
            $table->string('bank_details')->nullable()->after('salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('contact_no');
            $table->dropColumn('joining_date');
            $table->dropColumn('em_contact_person');
            $table->dropColumn('em_contact_no');
            $table->dropColumn('nid');
            $table->dropColumn('address');
            $table->dropColumn('salary');
            $table->dropColumn('bank_details');
        });
    }
};
