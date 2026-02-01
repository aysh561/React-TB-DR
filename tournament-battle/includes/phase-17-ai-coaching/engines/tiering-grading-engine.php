<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/engines/tiering-grading-engine.php
 *
 * PURPOSE (STRICT):
 * Phase 17 ke internal engines ke outputs ko consume kar ke
 * player ka coaching-only Tier & Grade determine karta hai.
 *
 * ❌ No enforcement
 * ❌ No rewards / penalties
 * ❌ No matchmaking impact
 * ❌ No persistence
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
if (defined('PHASE_17_TIERING_GRADING_ENGINE_LOADED')) {
    return;
}
define('PHASE_17_TIERING_GRADING_ENGINE_LOADED', true);

// ===============================
// DEPENDENCY CHECK (PHASE 17 ONLY)
// ===============================
if (
    !function_exists('phase17_build_player_skill_profile') ||
    !function_exists('phase17_evaluate_shot_quality') ||
    !function_exists('phase17_analyze_decisions') ||
    !function_exists('phase17_analyze_behavioral_tempo')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'engine' => 'tiering-grading-engine'
        ]);
    }
    return;
}

// ===============================
// TIERING & GRADING ENGINE
// ===============================

/**
 * Player ka Tier aur Grade determine karta hai
 * @return array
 */
function phase17_build_player_tier_grade(): array
{
    // ---- Inputs (read-only, Phase 17 internal engines) ----
    $skillProfile = phase17_build_player_skill_profile();
    $shots        = phase17_evaluate_shot_quality();
    $decisions    = phase17_analyze_decisions();
    $tempo        = phase17_analyze_behavioral_tempo();

    // ---- Extract normalized metrics ----
    $dimensions = $skillProfile['dimensions'] ?? [];

    $avgShotQuality = phase17_avg_shot_quality($shots);
    $decisionScore  = $decisions['summary']['optimal_ratio'] ?? 0.0;
    $tempoScore     = $tempo['summary']['avg_rhythm'] ?? 0.0;

    // ---- Composite score (transparent & deterministic) ----
    $composite =
        (array_sum($dimensions) / max(1, count($dimensions))) * 0.4 +
        $avgShotQuality * 0.25 +
        $decisionScore  * 0.2 +
        $tempoScore     * 0.15;

    $composite = max(0.0, min(1.0, $composite));

    return [
        'tier'  => phase17_map_tier($composite),
        'grade' => phase17_map_grade($composite),
        'score' => $composite,
        'snapshot' => [
            'skill_dimensions' => $dimensions,
            'avg_shot_quality' => $avgShotQuality,
            'decision_quality' => $decisionScore,
            'tempo_rhythm'     => $tempoScore,
        ],
        'meta' => [
            'phase'   => 17,
            'engine'  => 'tiering-grading-engine',
            'purpose' => 'coaching_only',
            'stable'  => true,
        ],
    ];
}

// ===============================
// INTERNAL HELPERS — PURE MAPPING
// ===============================

function phase17_avg_shot_quality(array $shots): float
{
    if (empty($shots)) {
        return 0.0;
    }

    $sum = 0.0;
    $count = 0;

    foreach ($shots as $s) {
        if (!is_array($s)) {
            continue;
        }
        $sum += (
            ($s['precision'] ?? 0.0) +
            ($s['timing'] ?? 0.0) +
            ($s['control'] ?? 0.0) +
            ($s['alignment'] ?? 0.0)
        ) / 4;
        $count++;
    }

    return $count > 0 ? max(0.0, min(1.0, $sum / $count)) : 0.0;
}

function phase17_map_tier(float $score): string
{
    if ($score >= 0.85) {
        return 'Platinum';
    }
    if ($score >= 0.70) {
        return 'Gold';
    }
    if ($score >= 0.55) {
        return 'Silver';
    }
    return 'Bronze';
}

function phase17_map_grade(float $score): string
{
    if ($score >= 0.85) {
        return 'A';
    }
    if ($score >= 0.70) {
        return 'B';
    }
    if ($score >= 0.55) {
        return 'C';
    }
    if ($score >= 0.40) {
        return 'D';
    }
    return 'E';
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'tiering-grading-engine'
    ]);
}

return;
