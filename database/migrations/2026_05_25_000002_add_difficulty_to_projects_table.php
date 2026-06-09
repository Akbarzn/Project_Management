<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah kolom difficulty, estimated_hours, required_skill ke tabel projects
 *
 * - difficulty      : tingkat kesulitan project (1=mudah, 5=sangat sulit)
 * - estimated_hours : estimasi total jam pengerjaan project
 * - required_skill  : skill utama yang dibutuhkan untuk project ini
 *                     digunakan sebagai filter utama saat auto-assignment
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Tingkat kesulitan: 1 (mudah) sampai 5 (sangat sulit)
            $table->integer('difficulty')->default(1)->after('is_approved')
                  ->comment('Tingkat kesulitan project: 1 (mudah) - 5 (sangat sulit)');

            // Estimasi jam pengerjaan oleh satu karyawan
            $table->integer('estimated_hours')->default(0)->after('difficulty')
                  ->comment('Estimasi total jam pengerjaan project');

            // Skill utama yang dibutuhkan untuk project
            $table->string('required_skill')->nullable()->after('estimated_hours')
                  ->comment('Skill utama yang dibutuhkan, dipakai untuk auto-assignment');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'estimated_hours', 'required_skill']);
        });
    }
};
