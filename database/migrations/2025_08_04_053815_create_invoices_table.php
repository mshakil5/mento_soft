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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->string('invoice_number')->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('vat_percent')->default(0);
            $table->string('vat_amount')->default(0);
            $table->string('subtotal')->default(0);
            $table->string('discount_percent')->default(0);
            $table->string('discount_amount')->default(0);
            $table->string('net_amount')->default(0);
            $table->boolean('status')->default(1); // 1 = pending, 2 = paid
            $table->longText('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
