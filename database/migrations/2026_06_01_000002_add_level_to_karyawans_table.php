<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom level ke tabel karyawans.
 *
 * - level : enum Junior | Intermediate | Senior | Lead
 *
 * Digunakan oleh AutoAssignmentService untuk memfilter kandidat
 * berdasarkan difficulty project request.
 *
 * Difficulty Mapping:
 *   Difficulty 1 → Junior
 *   Difficulty 2 → Junior
 *   Difficulty 3 → Intermediate
 *   Difficulty 4 → Senior
 *   Difficulty 5 → Lead atau Senior (dengan fallback)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->enum('level', ['Junior', 'Intermediate', 'Senior', 'Lead'])
                  ->default('Junior')
                  ->after('job_title')
                  ->comment('Level karyawan: Junior, Intermediate, Senior, Lead');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
