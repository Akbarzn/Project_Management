<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom priority, difficulty, estimated_duration ke tabel project_requests.
 *
 * - priority           : tingkat prioritas (1=Low, 2=Normal, 3=Medium, 4=High, 5=Critical)
 * - difficulty         : tingkat kesulitan (1=Sangat Mudah ... 5=Sangat Sulit)
 * - estimated_duration : estimasi durasi dalam jam
 *
 * Ketiga field ini digunakan oleh AutoAssignmentService untuk menghitung
 * TaskWeight = priority × difficulty × estimated_duration
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            // 1=Low, 2=Normal, 3=Medium, 4=High, 5=Critical
            $table->unsignedTinyInteger('priority')
                  ->default(1)
                  ->after('status')
                  ->comment('Prioritas: 1=Low, 2=Normal, 3=Medium, 4=High, 5=Critical');

            // 1=Sangat Mudah ... 5=Sangat Sulit
            $table->unsignedTinyInteger('difficulty')
                  ->default(1)
                  ->after('priority')
                  ->comment('Kesulitan: 1=Sangat Mudah, 2=Mudah, 3=Sedang, 4=Sulit, 5=Sangat Sulit');

            // Estimasi durasi dalam jam
            $table->unsignedInteger('estimated_duration')
                  ->default(0)
                  ->after('difficulty')
                  ->comment('Estimasi durasi pengerjaan dalam satuan jam');
        });
    }

    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropColumn(['priority', 'difficulty', 'estimated_duration']);
        });
    }
};
