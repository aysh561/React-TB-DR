<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/engines/decision-analysis-engine.php
 *
 * PURPOSE (STRICT):
 * Player ke in-match decisions ki quality analyze karta hai
 * sirf READ-ONLY inputs par based.
 *
 * ❌ No enforcement
 * ❌ No penalties
 * ❌ No persistence
 * ❌ No coaching advice
 * ❌ No tiering / grading
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
if (defined('PHASE_17_DECISION_ANALYSIS_ENGINE_LOADED')) {
    return;
}
define('PHASE_17_DECISION_ANALYSIS_ENGINE_LOADED', true);

// ===============================
// DEPENDENCY CHECK (READ-ONLY)
// ===============================
if (
    !function_exists('phase17_get_action_stream') ||
    !function_exists('phase17_get_match_timeline') ||
    !function_exists('phase17_get_anti_cheat_verdict')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'engine' => 'decision-analysis-engine'
        ]);
    }
    return;
}

// ===============================
// DECISION ANALYSIS ENGINE — PURE COMPUTATION
// ===============================

/**
 * Per-decision analysis + lightweight aggregate summary
 * @return array
 */
function phase17_analyze_decisions(): array
{
    $actions  = phase17_get_action_stream();
    $timeline = phase17_get_match_timeline();
    $verdict  = phase17_get_anti_cheat_verdict(); // context only

    if (empty($actions)) {
        return [
            'decisions' => [],
            'summary'   => phase17_empty_decision_summary(),
        ];
    }

    $evaluations = [];

    foreach ($actions as $index => $action) {
        if (!is_array($action) || !isset($action['decision'])) {
            continue;
        }

        $evaluations[] = [
            'decision_index' => $index,
            'risk_reward'    => phase17_score_risk_reward($action),
            'context_awareness' => phase17_score_context_awareness($action, $timeline),
            'consistency'    => phase17_score_decision_consistency($action),
            'optimality'     => phase17_score_decision_optimality($action),
            'meta' => [
                'phase'   => 17,
                'engine'  => 'decision-analysis-engine',
                'verdict' => $verdict['status'] ?? null,
                'readonly'=> true,
            ],
        ];
    }

    return [
        'decisions' => $evaluations,
        'summary'   => phase17_build_decision_summary($evaluations),
    ];
}

// ===============================
// DECISION SCORING HELPERS
// (Pure, deterministic, side-effect free)
// ===============================

function phase17_score_risk_reward(array $action): float
{
    if (!isset($action['risk'], $action['reward'])) {
        return 0.0;
    }

    $risk   = max(0.0, (float)$action['risk']);
    $reward = max(0.0, (float)$action['reward']);

    if ($risk + $reward === 0.0) {
        return 0.0;
    }

    return max(0.0, min(1.0, $reward / ($risk + $reward)));
}

function phase17_score_context_awareness(array $action, array $timeline): float
{
    if (!isset($action['timestamp']) || empty($timeline)) {
        return 0.0;
    }

    $localEvents = 0;
    foreach ($timeline as $event) {
        if (!isset($event['timestamp'])) {
            continue;
        }
        if (abs($event['timestamp'] - $action['timestamp']) <= 3) {
            $localEvents++;
        }
    }

    return max(0.0, min(1.0, 1 / (1 + abs($localEvents - 1))));
}

function phase17_score_decision_consistency(array $action): float
{
    if (!isset($action['decision_confidence'])) {
        return 0.0;
    }

    $conf = (float)$action['decision_confidence'];
    return max(0.0, min(1.0, $conf));
}

function phase17_score_decision_optimality(array $action): float
{
    if (!isset($action['decision'])) {
        return 0.0;
    }

    return ($action['decision'] === 'optimal') ? 1.0 : 0.0;
}

// ===============================
// SUMMARY BUILDERS (LIGHTWEIGHT)
// ===============================

function phase17_build_decision_summary(array $evaluations): array
{
    if (empty($evaluations)) {
        return phase17_empty_decision_summary();
    }

    $count = count($evaluations);
    $sums = [
        'risk_reward' => 0.0,
        'context_awareness' => 0.0,
        'consistency' => 0.0,
        'optimality'  => 0.0,
    ];

    foreach ($evaluations as $e) {
        foreach ($sums as $k => $_) {
            $sums[$k] += $e[$k] ?? 0.0;
        }
    }

    return [
        'total_decisions' => $count,
        'avg_risk_reward' => $sums['risk_reward'] / $count,
        'avg_context_awareness' => $sums['context_awareness'] / $count,
        'avg_consistency' => $sums['consistency'] / $count,
        'optimal_ratio'  => $sums['optimality'] / $count,
    ];
}

function phase17_empty_decision_summary(): array
{
    return [
        'total_decisions' => 0,
        'avg_risk_reward' => 0.0,
        'avg_context_awareness' => 0.0,
        'avg_consistency' => 0.0,
        'optimal_ratio'  => 0.0,
    ];
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'decision-analysis-engine'
    ]);
}

return;
