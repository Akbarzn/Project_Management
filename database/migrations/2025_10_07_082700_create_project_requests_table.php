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
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name_project');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('tiket')->unique();
            $table->string('kategori');
            $table->text('description');
            $table->enum('status', ['pending','approve','rejected'])->default('pending');
            $table->string('document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
