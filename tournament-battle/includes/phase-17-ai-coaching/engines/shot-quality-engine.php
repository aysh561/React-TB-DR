<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/engines/shot-quality-engine.php
 *
 * PURPOSE (STRICT):
 * Har shot / action ki technical quality evaluate karta hai
 * sirf READ-ONLY inputs par based.
 *
 * ❌ No enforcement
 * ❌ No penalties
 * ❌ No persistence
 * ❌ No coaching advice
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
if (defined('PHASE_17_SHOT_QUALITY_ENGINE_LOADED')) {
    return;
}
define('PHASE_17_SHOT_QUALITY_ENGINE_LOADED', true);

// ===============================
// DEPENDENCY CHECK (READ-ONLY)
// ===============================
if (
    !function_exists('phase17_get_action_stream') ||
    !function_exists('phase17_get_temporal_markers') ||
    !function_exists('phase17_get_anti_cheat_verdict')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'engine' => 'shot-quality-engine'
        ]);
    }
    return;
}

// ===============================
// SHOT QUALITY ENGINE — PURE COMPUTATION
// ===============================

/**
 * Har shot ki quality evaluate karta hai
 * @return array
 */
function phase17_evaluate_shot_quality(): array
{
    $actions  = phase17_get_action_stream();
    $markers  = phase17_get_temporal_markers();
    $verdict  = phase17_get_anti_cheat_verdict(); // context only

    if (empty($actions)) {
        return [];
    }

    $results = [];

    foreach ($actions as $index => $shot) {
        if (!is_array($shot)) {
            continue;
        }

        $results[] = [
            'shot_index' => $index,
            'precision' => phase17_score_shot_precision($shot),
            'timing'    => phase17_score_shot_timing($shot, $markers),
            'control'   => phase17_score_shot_control($shot),
            'alignment' => phase17_score_shot_outcome_alignment($shot),
            'meta' => [
                'phase'    => 17,
                'engine'   => 'shot-quality-engine',
                'verdict'  => $verdict['status'] ?? null,
                'readonly' => true,
            ],
        ];
    }

    return $results;
}

// ===============================
// INTERNAL SHOT SCORING HELPERS
// (Pure, deterministic, side-effect free)
// ===============================

function phase17_score_shot_precision(array $shot): float
{
    if (!isset($shot['target_error'])) {
        return 0.0;
    }

    $error = abs((float)$shot['target_error']);
    return max(0.0, min(1.0, 1 / (1 + $error)));
}

function phase17_score_shot_timing(array $shot, array $markers): float
{
    if (!isset($shot['timestamp']) || empty($markers)) {
        return 0.0;
    }

    $closest = null;
    foreach ($markers as $m) {
        if (!isset($m['timestamp'])) {
            continue;
        }
        $diff = abs($shot['timestamp'] - $m['timestamp']);
        if ($closest === null || $diff < $closest) {
            $closest = $diff;
        }
    }

    if ($closest === null) {
        return 0.0;
    }

    return max(0.0, min(1.0, 1 / (1 + $closest)));
}

function phase17_score_shot_control(array $shot): float
{
    if (!isset($shot['control_variance'])) {
        return 0.0;
    }

    $variance = abs((float)$shot['control_variance']);
    return max(0.0, min(1.0, 1 / (1 + $variance)));
}

function phase17_score_shot_outcome_alignment(array $shot): float
{
    if (!isset($shot['intended'], $shot['outcome'])) {
        return 0.0;
    }

    return ($shot['intended'] === $shot['outcome']) ? 1.0 : 0.0;
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'shot-quality-engine'
    ]);
}

return;
