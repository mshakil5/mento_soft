<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_email_logs', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
            $table->longText('message')->change();
            $table->string('attachment')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('client_email_logs', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->text('message')->change();
            $table->dropColumn('attachment');
        });
    }
};
