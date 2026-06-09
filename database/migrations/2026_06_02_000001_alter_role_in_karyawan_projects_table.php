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
        Schema::table('karyawan_projects', function (Blueprint $table) {
            // Ubah tipe kolom role dari enum menjadi string agar lebih fleksibel 
            // menampung role 'Business Analyst' serta role kustom lainnya.
            $table->string('role')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_projects', function (Blueprint $table) {
            $table->enum('role', [
                'Analisis Proses Bisnis',
                'Programmer',
                'Database Functional',
                'Quality Test',
                'SysAdmin',
            ])->nullable()->change();
        });
    }
};
