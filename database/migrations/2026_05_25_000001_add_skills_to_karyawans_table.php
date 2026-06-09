<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah kolom skills dan max_workload ke tabel karyawans
 *
 * - skills      : array skill karyawan disimpan dalam format JSON
 *                 contoh: ["Laravel", "PHP", "MySQL"]
 * - max_workload: batas maksimum jam kerja aktif sebelum dianggap overload
 *                 default 40 jam (setara 1 minggu kerja penuh)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Simpan skill sebagai JSON agar fleksibel dan bisa multi-skill
            $table->json('skills')->nullable()->after('job_title')
                  ->comment('Array skill karyawan, contoh: ["Laravel","PHP","MySQL"]');

            // Batas workload sebelum karyawan dianggap overload
            $table->integer('max_workload')->default(40)->after('skills')
                  ->comment('Batas maksimum jam kerja aktif (default: 40 jam)');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['skills', 'max_workload']);
        });
    }
};
