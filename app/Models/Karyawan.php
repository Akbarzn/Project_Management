<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     * Mendefinisikan secara eksplisit lebih aman daripada $guarded = []
     * @var array<string>
     */
    /** Level enum yang tersedia */
    public const LEVELS = ['Junior', 'Intermediate', 'Senior', 'Lead'];

    /**
     * Urutan level untuk fallback (index semakin kecil = semakin junior).
     * Dipakai AutoAssignmentService untuk cari level di bawahnya.
     */
    public const LEVEL_ORDER = [
        'Junior'       => 1,
        'Intermediate' => 2,
        'Senior'       => 3,
        'Lead'         => 4,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'nik',
        'jabatan',
        'phone',
        'job_title',
        'level',        // Junior | Intermediate | Senior | Lead
        'cost',
        'skills',       // JSON array skill karyawan, contoh: ["Laravel","PHP"]
        'max_workload', // Batas jam kerja aktif sebelum dianggap overload
    ];

    /**
     * Type casting untuk kolom:
     * - skills  : otomatis di-decode dari JSON ke PHP array saat diakses
     * @var array<string, string>
     */
    protected $casts = [
        'skills'       => 'array',
        'max_workload' => 'integer',
    ];

    /**
     * Kembalikan integer urutan level untuk perbandingan.
     * Contoh: 'Senior' → 3
     */
    public function getLevelOrderAttribute(): int
    {
        return self::LEVEL_ORDER[$this->level] ?? 1;
    }

    /**
     * Summary of user
     * relasi one to one
     * client dgn karyawan
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Karyawan>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of projects
     * relasi many to many
     * karyawan dgn project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Project, Karyawan, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'karyawan_projects', 'karyawan_id', 'project_id')
        // simpan cost dan job_title snapshot agar tidak berubah kalo cost dan job_title karyawan diupdate atau berubah
            ->withPivot('cost_snapshot', 'job_title_snapshot')
            // otomatis isi created_at dan update_at utk pivot table
            ->withTimestamps();
    }

    /**
     * Summary of tasks
     * relasi one to many
     * karyawan dgn task
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Task, Karyawan>
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Relasi ke task_work_logs melalui tasks.
     * Digunakan oleh WorkloadService untuk menghitung total jam kerja aktif.
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function workLogs()
    {
        return $this->hasManyThrough(
            TaskWorkLog::class,
            Task::class,
            'karyawan_id', // FK di tasks
            'task_id',     // FK di task_work_logs
            'id',          // PK di karyawans
            'id'           // PK di tasks
        );
    }

    /**
     * Cek apakah karyawan memiliki skill tertentu.
     * Menggunakan in_array karena skills di-cast sebagai PHP array.
     * @param string $skill
     * @return bool
     */
    public function hasSkill(string $skill): bool
    {
        if (empty($this->skills)) {
            return false;
        }

        // Case-insensitive matching agar "laravel" == "Laravel"
        return collect($this->skills)
            ->map(fn($s) => strtolower(trim($s)))
            ->contains(strtolower(trim($skill)));
    }

    /**
     * Accessor untuk menampilkan skill dalam format string dipisahkan koma.
     *
     * @return string
     */
    public function getSkillsTextAttribute(): string
    {
        return is_array($this->skills) ? implode(', ', $this->skills) : '';
    }
}
