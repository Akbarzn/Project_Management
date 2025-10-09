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
            $table->foreignId('client_id')->contrained('clients')->onDelete('cascade');
            $table->foreignId('request_id')->nullable()->contrained('project_requests')->onDelete('set null');
            $table->date('start_date_project');
            $table->date('finish_date_project');
            $table->string('status', 20)->default('pending');
            $table->foreignId('created_by')->nullable()->contrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->contrained('users')->onDelete('set null');
            $table->boolean('is_approved')->default(false);
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
