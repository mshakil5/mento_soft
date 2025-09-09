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
        Schema::create('service_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_service_detail_id')->nullable()->constrained('project_service_details')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('service_renewals');
    }
};
