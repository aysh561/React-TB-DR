<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/analytics/reputation-metrics-engine.php
 */

// ===============================
// SECURITY GUARDS
// ===============================
if (php_sapi_name() !== 'cli' && !defined('WPINC')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

// ===============================
// IDEMPOTENT LOAD PROTECTION
// ===============================
if (defined('PHASE_17_REPUTATION_METRICS_ENGINE_LOADED')) {
    return;
}
define('PHASE_17_REPUTATION_METRICS_ENGINE_LOADED', true);

// ===============================
// DEPENDENCY CHECK (PHASE 17 ONLY)
// ===============================
if (
    !function_exists('phase17_generate_coaching_report') ||
    !function_exists('phase17_build_player_tier_grade') ||
    !function_exists('phase17_track_cross_match_progress')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'engine' => 'reputation-metrics-engine'
        ]);
    }
    return;
}

// ===============================
// REPUTATION METRICS ENGINE
// ===============================

function phase17_build_reputation_metrics(
    array $crossMatchProgress
): array {
    $report    = phase17_generate_coaching_report();
    $tierGrade = phase17_build_player_tier_grade();

    $dimensionsTrend = $crossMatchProgress['dimensions'] ?? [];

    return [
        'consistency_reputation'   => phase17_calc_consistency_rep($dimensionsTrend),
        'improvement_trajectory'   => phase17_calc_improvement_rep($dimensionsTrend),
        'stability_volatility'     => phase17_calc_stability_rep($dimensionsTrend),
        'coaching_responsiveness'  => phase17_calc_responsiveness_rep($report, $crossMatchProgress),
        'context' => [
            'tier'  => $tierGrade['tier'] ?? null,
            'grade' => $tierGrade['grade'] ?? null,
        ],
        'meta' => [
            'phase'  => 17,
            'engine' => 'reputation-metrics-engine',
            'stable' => true,
        ],
    ];
}

// ===============================
// METRIC CALCULATORS (PURE)
// ===============================

function phase17_calc_consistency_rep(array $dimensionTrends): float
{
    if (empty($dimensionTrends)) {
        return 0.0;
    }

    $stable = 0;
    foreach ($dimensionTrends as $d) {
        if (($d['trend'] ?? '') === 'stable') {
            $stable++;
        }
    }

    return max(0.0, min(1.0, $stable / count($dimensionTrends)));
}

function phase17_calc_improvement_rep(array $dimensionTrends): float
{
    if (empty($dimensionTrends)) {
        return 0.0;
    }

    $improving = 0;
    foreach ($dimensionTrends as $d) {
        if (($d['trend'] ?? '') === 'improving') {
            $improving++;
        }
    }

    return max(0.0, min(1.0, $improving / count($dimensionTrends)));
}

function phase17_calc_stability_rep(array $dimensionTrends): float
{
    if (empty($dimensionTrends)) {
        return 0.0;
    }

    $sumVariance = 0.0;
    foreach ($dimensionTrends as $d) {
        $avg = abs($d['average_delta'] ?? 0.0);
        $sumVariance += $avg;
    }

    $norm = $sumVariance / count($dimensionTrends);
    return max(0.0, min(1.0, 1 / (1 + $norm)));
}

function phase17_calc_responsiveness_rep(array $report, array $progress): float
{
    $improvements = count($report['improvement_areas'] ?? []);
    $trends       = count($progress['dimensions'] ?? []);

    if ($trends === 0) {
        return 0.0;
    }

    $resolved = 0;
    foreach ($progress['dimensions'] as $d) {
        if (($d['trend'] ?? '') === 'improving') {
            $resolved++;
        }
    }

    return max(0.0, min(1.0, $resolved / max(1, $improvements)));
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'reputation-metrics-engine'
    ]);
}

return;
