<?php

/*
|--------------------------------------------------------------------------
| Test Case – Workload Balancing Testing Suite
|--------------------------------------------------------------------------
|
| Konfigurasi global untuk semua test dalam project ini.
|
| Arsitektur test yang digunakan:
| - Unit Test   : tests/Unit/ → Menguji logic terisolasi, tanpa DB, pakai Mock
| - Feature Test: tests/Feature/ → Menguji endpoint HTTP end-to-end, pakai DB
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Custom Expectations
|--------------------------------------------------------------------------
*/

// Validasi workload score tidak negatif
expect()->extend('toBeValidWorkload', function () {
    return $this->toBeGreaterThanOrEqual(0);
});

// Validasi capacity_pct selalu antara 0 dan nilai positif
expect()->extend('toBeValidCapacityPct', function () {
    return $this->toBeGreaterThanOrEqual(0);
});

/*
|--------------------------------------------------------------------------
| Global Helper Functions untuk Test
|--------------------------------------------------------------------------
*/

/**
 * Hitung workload secara manual menggunakan formula yang sama dengan service.
 * Berguna untuk validasi hasil kalkulasi di test.
 *
 * Formula: workload = activeHours + (activeProjects × 5)
 */
function calculateExpectedWorkload(float $activeHours, int $activeProjects): float
{
    return $activeHours + ($activeProjects * 5);
}

/**
 * Hitung capacity_pct secara manual.
 */
function calculateExpectedCapacityPct(float $workload, int $maxWorkload): float
{
    if ($maxWorkload <= 0) return 100.0;
    return round(($workload / $maxWorkload) * 100, 1);
}
