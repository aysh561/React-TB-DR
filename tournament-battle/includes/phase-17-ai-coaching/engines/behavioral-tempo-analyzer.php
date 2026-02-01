<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/engines/behavioral-tempo-analyzer.php
 *
 * PURPOSE (STRICT):
 * Player ke pace, rhythm aur temporal behavior ka analysis
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
if (defined('PHASE_17_BEHAVIORAL_TEMPO_ANALYZER_LOADED')) {
    return;
}
define('PHASE_17_BEHAVIORAL_TEMPO_ANALYZER_LOADED', true);

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
            'engine' => 'behavioral-tempo-analyzer'
        ]);
    }
    return;
}

// ===============================
// BEHAVIORAL TEMPO ANALYZER — PURE COMPUTATION
// ===============================

/**
 * Tempo & rhythm analysis with per-segment metrics
 * @return array
 */
function phase17_analyze_behavioral_tempo(): array
{
    $actions  = phase17_get_action_stream();
    $markers  = phase17_get_temporal_markers();
    $verdict  = phase17_get_anti_cheat_verdict(); // context only

    if (empty($actions) || empty($markers)) {
        return [
            'segments' => [],
            'summary'  => phase17_empty_tempo_summary(),
        ];
    }

    $segments = phase17_segment_actions_by_markers($actions, $markers);
    $results  = [];

    foreach ($segments as $idx => $segmentActions) {
        $results[] = [
            'segment_index' => $idx,
            'frequency'     => phase17_score_action_frequency($segmentActions),
            'rhythm'        => phase17_score_rhythm_stability($segmentActions),
            'burst_idle'    => phase17_score_burst_idle_balance($segmentActions),
            'pressure'      => phase17_score_time_pressure_response($segmentActions),
            'meta' => [
                'phase'   => 17,
                'engine'  => 'behavioral-tempo-analyzer',
                'verdict' => $verdict['status'] ?? null,
                'readonly'=> true,
            ],
        ];
    }

    return [
        'segments' => $results,
        'summary'  => phase17_build_tempo_summary($results),
    ];
}

// ===============================
// TEMPO SCORING HELPERS
// ===============================

function phase17_score_action_frequency(array $actions): float
{
    $count = count($actions);
    if ($count === 0) {
        return 0.0;
    }

    return max(0.0, min(1.0, 1 - (1 / (1 + $count))));
}

function phase17_score_rhythm_stability(array $actions): float
{
    if (count($actions) < 2) {
        return 0.0;
    }

    $intervals = [];
    $lastTs = null;

    foreach ($actions as $a) {
        if (!isset($a['timestamp'])) {
            continue;
        }
        if ($lastTs !== null) {
            $intervals[] = abs($a['timestamp'] - $lastTs);
        }
        $lastTs = $a['timestamp'];
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

    return max(0.0, min(1.0, 1 / (1 + $variance)));
}

function phase17_score_burst_idle_balance(array $actions): float
{
    if (count($actions) < 2) {
        return 0.0;
    }

    $bursts = 0;
    $idles  = 0;
    $lastTs = null;

    foreach ($actions as $a) {
        if (!isset($a['timestamp'])) {
            continue;
        }
        if ($lastTs !== null) {
            $gap = abs($a['timestamp'] - $lastTs);
            if ($gap <= 2) {
                $bursts++;
            } else {
                $idles++;
            }
        }
        $lastTs = $a['timestamp'];
    }

    $total = $bursts + $idles;
    if ($total === 0) {
        return 0.0;
    }

    return max(0.0, min(1.0, 1 - abs($bursts - $idles) / $total));
}

function phase17_score_time_pressure_response(array $actions): float
{
    $pressureEvents = 0;
    $responses      = 0;

    foreach ($actions as $a) {
        if (!isset($a['under_pressure'])) {
            continue;
        }
        $pressureEvents++;
        if (!empty($a['response_time']) && $a['response_time'] <= 1.5) {
            $responses++;
        }
    }

    if ($pressureEvents === 0) {
        return 0.0;
    }

    return max(0.0, min(1.0, $responses / $pressureEvents));
}

// ===============================
// SEGMENTATION & SUMMARY
// ===============================

function phase17_segment_actions_by_markers(array $actions, array $markers): array
{
    $segments = [];
    $count = count($markers);

    for ($i = 0; $i < $count; $i++) {
        $start = $markers[$i]['timestamp'] ?? null;
        $end   = $markers[$i + 1]['timestamp'] ?? null;

        $segments[$i] = array_values(array_filter($actions, function ($a) use ($start, $end) {
            if (!isset($a['timestamp'])) {
                return false;
            }
            if ($start !== null && $a['timestamp'] < $start) {
                return false;
            }
            if ($end !== null && $a['timestamp'] >= $end) {
                return false;
            }
            return true;
        }));
    }

    return $segments;
}

function phase17_build_tempo_summary(array $segments): array
{
    if (empty($segments)) {
        return phase17_empty_tempo_summary();
    }

    $count = count($segments);
    $sum = [
        'frequency'  => 0.0,
        'rhythm'     => 0.0,
        'burst_idle' => 0.0,
        'pressure'   => 0.0,
    ];

    foreach ($segments as $s) {
        foreach ($sum as $k => $_) {
            $sum[$k] += $s[$k] ?? 0.0;
        }
    }

    return [
        'segments' => $count,
        'avg_frequency'  => $sum['frequency'] / $count,
        'avg_rhythm'     => $sum['rhythm'] / $count,
        'avg_burst_idle' => $sum['burst_idle'] / $count,
        'avg_pressure'   => $sum['pressure'] / $count,
    ];
}

function phase17_empty_tempo_summary(): array
{
    return [
        'segments' => 0,
        'avg_frequency'  => 0.0,
        'avg_rhythm'     => 0.0,
        'avg_burst_idle' => 0.0,
        'avg_pressure'   => 0.0,
    ];
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'behavioral-tempo-analyzer'
    ]);
}

return;
