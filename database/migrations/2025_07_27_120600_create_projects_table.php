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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('sub_title')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('image')->nullable();
            $table->string('demo_video')->nullable();
            $table->string('project_url')->nullable();
            $table->text('short_desc')->nullable();
            $table->longText('long_desc')->nullable();
            $table->text('technologies_used')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_featured')->default(0);
            $table->integer('sl')->default(0);
            $table->integer('views')->default(0);
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('meta_keywords')->nullable(); // comma-separated
            $table->string('meta_image')->nullable();
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
        Schema::dropIfExists('projects');
    }
};
