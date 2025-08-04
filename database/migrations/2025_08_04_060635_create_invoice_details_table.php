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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignId('client_project_id')->nullable()->constrained('client_projects')->onDelete('cascade');
            $table->string('project_name')->nullable();
            $table->longText('description')->nullable();
            $table->string('qty')->default(0);
            $table->string('unit_price')->default(0);
            $table->string('vat_percent')->default(0);
            $table->string('vat_amount')->default(0);
            $table->string('total_exc_vat')->default(0);
            $table->string('total_inc_vat')->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('invoice_details');
    }
};
