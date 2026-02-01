<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/engines/player-skill-model.php
 *
 * PURPOSE (STRICT):
 * Player Skill Model define karta hai jo
 * Phase 15 (real-time data) aur Phase 16 (anti-cheat verdicts)
 * ke READ-ONLY outputs par based hota hai.
 *
 * ❌ No enforcement
 * ❌ No penalties
 * ❌ No mutation
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
if (defined('PHASE_17_PLAYER_SKILL_MODEL_LOADED')) {
    return;
}
define('PHASE_17_PLAYER_SKILL_MODEL_LOADED', true);

// ===============================
// DEPENDENCY CHECK (READ-ONLY)
// ===============================
if (
    !function_exists('phase17_get_match_timeline') ||
    !function_exists('phase17_get_action_stream') ||
    !function_exists('phase17_get_temporal_markers') ||
    !function_exists('phase17_get_anti_cheat_verdict') ||
    !function_exists('phase17_get_integrity_flags') ||
    !function_exists('phase17_get_anti_cheat_confidence')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'engine' => 'player-skill-model'
        ]);
    }
    return;
}

// ===============================
// SKILL MODEL — PURE COMPUTATION
// ===============================

/**
 * Player skill profile generate karta hai
 * @return array
 */
function phase17_build_player_skill_profile(): array
{
    // ---- Read-only inputs ----
    $timeline   = phase17_get_match_timeline();
    $actions    = phase17_get_action_stream();
    $markers    = phase17_get_temporal_markers();

    $verdict    = phase17_get_anti_cheat_verdict();
    $flags      = phase17_get_integrity_flags();
    $confidence = phase17_get_anti_cheat_confidence();

    // ---- Skill dimensions (normalized 0.0 – 1.0) ----
    $consistency     = phase17_score_consistency($timeline, $actions);
    $accuracy        = phase17_score_accuracy($actions);
    $decisionQuality = phase17_score_decision_quality($actions, $markers);
    $tempoControl    = phase17_score_tempo_control($timeline, $markers);

    return [
        'dimensions' => [
            'consistency'      => $consistency,
            'accuracy'         => $accuracy,
            'decision_quality' => $decisionQuality,
            'tempo_control'    => $tempoControl,
        ],
        'integrity_context' => [
            'verdict'    => $verdict,
            'flags'      => $flags,
            'confidence' => $confidence,
        ],
        'meta' => [
            'model'  => 'player-skill-model',
            'phase'  => 17,
            'stable' => true,
        ],
    ];
}

// ===============================
// INTERNAL SCORING HELPERS
// (Pure, deterministic, no side-effects)
// ===============================

function phase17_score_consistency(array $timeline, array $actions): float
{
    if (empty($actions)) {
        return 0.0;
    }

    $intervals = [];
    $lastTs = null;

    foreach ($actions as $action) {
        if (!isset($action['timestamp'])) {
            continue;
        }
        if ($lastTs !== null) {
            $intervals[] = abs($action['timestamp'] - $lastTs);
        }
        $lastTs = $action['timestamp'];
    }

    if (empty($intervals)) {
        return 0.0;
    }

    $avg = array_sum($intervals) / count($intervals);
    $variance = 0.0;

    foreach ($intervals as $i) {
        $variance += pow($i - $avg, 2);
    }

    $variance /= count($intervals);

    // lower variance = higher consistency
    return max(0.0, min(1.0, 1 / (1 + $variance)));
}

function phase17_score_accuracy(array $actions): float
{
    if (empty($actions)) {
        return 0.0;
    }

    $total = 0;
    $hits  = 0;

    foreach ($actions as $action) {
        if (!isset($action['result'])) {
            continue;
        }
        $total++;
        if ($action['result'] === 'success') {
            $hits++;
        }
    }

    if ($total === 0) {
        return 0.0;
    }

    return max(0.0, min(1.0, $hits / $total));
}

function phase17_score_decision_quality(array $actions, array $markers): float
{
    if (empty($actions) || empty($markers)) {
        return 0.0;
    }

    $scored = 0;
    $good   = 0;

    foreach ($actions as $action) {
        if (!isset($action['timestamp'], $action['decision'])) {
            continue;
        }
        $scored++;
        if ($action['decision'] === 'optimal') {
            $good++;
        }
    }

    if ($scored === 0) {
        return 0.0;
    }

    return max(0.0, min(1.0, $good / $scored));
}

function phase17_score_tempo_control(array $timeline, array $markers): float
{
    if (empty($timeline) || empty($markers)) {
        return 0.0;
    }

    $segments = count($markers);
    if ($segments === 0) {
        return 0.0;
    }

    $eventsPerSegment = count($timeline) / $segments;

    return max(0.0, min(1.0, 1 / (1 + abs($eventsPerSegment - 1))));
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'player-skill-model'
    ]);
}

return;
