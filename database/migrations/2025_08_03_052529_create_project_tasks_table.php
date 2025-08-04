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
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_project_id')->nullable()->constrained('client_projects')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->longText('task')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->tinyInteger('status')->default(0); // 0 = incomplete, 1 = complete
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
        Schema::dropIfExists('project_tasks');
    }
};
