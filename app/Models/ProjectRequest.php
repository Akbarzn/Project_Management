<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Cast kolom numerik agar bisa dipakai langsung dalam kalkulasi
     * tanpa perlu konversi manual di Service.
     */
    protected $casts = [
        'priority'           => 'integer',
        'difficulty'         => 'integer',
        'estimated_duration' => 'integer',
    ];

    // ─── Priority Labels ────────────────────────────────────────────────────
    public const PRIORITY_LABELS = [
        1 => 'Low',
        2 => 'Medium',
        3 => 'High',
        4 => 'Critical',
    ];

    // ─── Priority Badge Colors (Tailwind) ───────────────────────────────────
    public const PRIORITY_BADGE = [
        1 => 'bg-gray-100 text-gray-800 border border-gray-200',
        2 => 'bg-blue-100 text-blue-800 border border-blue-200',
        3 => 'bg-orange-100 text-orange-800 border border-orange-200',
        4 => 'bg-red-100 text-red-800 border border-red-200',
    ];

    // ─── Difficulty Labels ──────────────────────────────────────────────────
    public const DIFFICULTY_LABELS = [
        1 => 'Sangat Mudah',
        2 => 'Mudah',
        3 => 'Sedang',
        4 => 'Sulit',
        5 => 'Sangat Sulit',
    ];

    // ─── Difficulty Badge Colors (Tailwind) ─────────────────────────────────
    public const DIFFICULTY_BADGES = [
        1 => 'bg-green-100 text-green-800 border border-green-200',
        2 => 'bg-blue-100 text-blue-800 border border-blue-200',
        3 => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        4 => 'bg-red-100 text-red-800 border border-red-200',
        5 => 'bg-gray-850 text-white border border-gray-800',
    ];

    // ─── Accessor helpers ───────────────────────────────────────────────────

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITY_LABELS[$this->priority] ?? 'Unknown';
    }

    public function getPriorityBadgeAttribute(): string
    {
        return self::PRIORITY_BADGE[$this->priority] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
    }

    public function getDifficultyLabelAttribute(): string
    {
        return self::DIFFICULTY_LABELS[$this->difficulty] ?? 'Unknown';
    }

    public function getDifficultyBadgeAttribute(): string
    {
        return self::DIFFICULTY_BADGES[$this->difficulty] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
    }

    /**
     * Hitung TaskWeight yang dipakai oleh AutoAssignmentService.
     * TaskWeight = priority × difficulty × estimated_duration
     */
    public function getTaskWeightAttribute(): int
    {
        return (int) ($this->priority * $this->difficulty * $this->estimated_duration);
    }

    // ─── Relations ──────────────────────────────────────────────────────────

    /**
     * Relasi many-to-one: ProjectRequest → Client
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Client, ProjectRequest>
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relasi one-to-many: ProjectRequest → Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project, ProjectRequest>
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'request_id');
    }
}
