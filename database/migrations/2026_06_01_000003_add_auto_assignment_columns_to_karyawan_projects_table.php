<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom role dan kolom auto-assignment ke tabel karyawan_projects.
 *
 * - role                 : role dalam tim project (5 role tetap)
 * - assigned_by_system   : apakah di-assign otomatis oleh sistem
 * - task_weight          : bobot task = priority × difficulty × estimated_duration
 * - projected_workload   : current_workload + task_weight saat di-assign
 * - fallback_used        : apakah menggunakan fallback level
 * - fallback_note        : catatan fallback jika level asli tidak tersedia
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan_projects', function (Blueprint $table) {
            $table->enum('role', [
                'Analisis Proses Bisnis',
                'Programmer',
                'Database Functional',
                'Quality Test',
                'SysAdmin',
            ])->nullable()->after('job_title_snapshot')
              ->comment('Role karyawan dalam project ini');

            $table->boolean('assigned_by_system')
                  ->default(false)
                  ->after('role')
                  ->comment('true jika di-assign otomatis oleh AutoAssignmentService');

            $table->unsignedInteger('task_weight')
                  ->default(0)
                  ->after('assigned_by_system')
                  ->comment('Bobot task = priority × difficulty × estimated_duration');

            $table->decimal('projected_workload', 10, 2)
                  ->default(0)
                  ->after('task_weight')
                  ->comment('Projected workload saat di-assign = current_workload + task_weight');

            $table->boolean('fallback_used')
                  ->default(false)
                  ->after('projected_workload')
                  ->comment('true jika tidak ada kandidat level sesuai dan sistem turun ke level bawah');

            $table->string('fallback_note')->nullable()
                  ->after('fallback_used')
                  ->comment('Catatan fallback, misal: Senior tidak tersedia → Intermediate digunakan');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan_projects', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'assigned_by_system',
                'task_weight',
                'projected_workload',
                'fallback_used',
                'fallback_note',
            ]);
        });
    }
};
