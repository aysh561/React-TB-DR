<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/coaching/coaching-report-generator.php
 *
 * PURPOSE (STRICT):
 * Phase 17 ke tamam analytical engines ke outputs ko consume kar ke
 * ek structured, human-readable coaching report assemble karta hai.
 *
 * ❌ No enforcement
 * ❌ No rewards / penalties
 * ❌ No UI rendering
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
if (defined('PHASE_17_COACHING_REPORT_GENERATOR_LOADED')) {
    return;
}
define('PHASE_17_COACHING_REPORT_GENERATOR_LOADED', true);

// ===============================
// DEPENDENCY CHECK (PHASE 17 ONLY)
// ===============================
if (
    !function_exists('phase17_build_player_skill_profile') ||
    !function_exists('phase17_evaluate_shot_quality') ||
    !function_exists('phase17_analyze_decisions') ||
    !function_exists('phase17_analyze_behavioral_tempo') ||
    !function_exists('phase17_build_player_tier_grade')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'module' => 'coaching-report-generator'
        ]);
    }
    return;
}

// ===============================
// COACHING REPORT GENERATOR
// ===============================

/**
 * Structured coaching report generate karta hai
 * @return array
 */
function phase17_generate_coaching_report(): array
{
    $skill     = phase17_build_player_skill_profile();
    $shots     = phase17_evaluate_shot_quality();
    $decisions = phase17_analyze_decisions();
    $tempo     = phase17_analyze_behavioral_tempo();
    $tierGrade = phase17_build_player_tier_grade();

    $dimensions = $skill['dimensions'] ?? [];

    return [
        'overview' => [
            'tier'  => $tierGrade['tier'] ?? null,
            'grade' => $tierGrade['grade'] ?? null,
            'score' => $tierGrade['score'] ?? 0.0,
        ],

        'strengths' => phase17_identify_strengths(
            $dimensions,
            $shots,
            $decisions,
            $tempo
        ),

        'improvement_areas' => phase17_identify_improvements(
            $dimensions,
            $shots,
            $decisions,
            $tempo
        ),

        'neutral_observations' => phase17_identify_neutral(
            $dimensions,
            $shots,
            $decisions,
            $tempo
        ),

        'raw_metrics' => [
            'skill_dimensions' => $dimensions,
            'shot_quality'     => $shots,
            'decision_analysis'=> $decisions,
            'tempo_analysis'   => $tempo,
        ],

        'meta' => [
            'phase'   => 17,
            'engine'  => 'coaching-report-generator',
            'purpose' => 'coaching_only',
            'stable'  => true,
        ],
    ];
}

// ===============================
// INSIGHT BUILDERS (PURE & TRACEABLE)
// ===============================

function phase17_identify_strengths(array $dimensions, array $shots, array $decisions, array $tempo): array
{
    $strengths = [];

    foreach ($dimensions as $k => $v) {
        if ($v >= 0.75) {
            $strengths[] = [
                'area'  => $k,
                'score' => $v,
                'basis' => 'skill_dimension'
            ];
        }
    }

    if (($decisions['summary']['optimal_ratio'] ?? 0.0) >= 0.7) {
        $strengths[] = [
            'area'  => 'decision_making',
            'score' => $decisions['summary']['optimal_ratio'],
            'basis' => 'decision_analysis'
        ];
    }

    return $strengths;
}

function phase17_identify_improvements(array $dimensions, array $shots, array $decisions, array $tempo): array
{
    $improvements = [];

    foreach ($dimensions as $k => $v) {
        if ($v > 0.0 && $v < 0.45) {
            $improvements[] = [
                'area'  => $k,
                'score' => $v,
                'basis' => 'skill_dimension'
            ];
        }
    }

    if (($tempo['summary']['avg_rhythm'] ?? 1.0) < 0.45) {
        $improvements[] = [
            'area'  => 'tempo_rhythm',
            'score' => $tempo['summary']['avg_rhythm'],
            'basis' => 'tempo_analysis'
        ];
    }

    return $improvements;
}

function phase17_identify_neutral(array $dimensions, array $shots, array $decisions, array $tempo): array
{
    $neutral = [];

    foreach ($dimensions as $k => $v) {
        if ($v >= 0.45 && $v < 0.75) {
            $neutral[] = [
                'area'  => $k,
                'score' => $v,
                'basis' => 'skill_dimension'
            ];
        }
    }

    return $neutral;
}

// ===============================
// MODULE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('module_loaded', [
        'phase'  => 17,
        'module' => 'coaching-report-generator'
    ]);
}

return;
