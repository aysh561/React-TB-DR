<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/analytics/cross-match-progress-tracker.php
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
if (defined('PHASE_17_CROSS_MATCH_PROGRESS_TRACKER_LOADED')) {
    return;
}
define('PHASE_17_CROSS_MATCH_PROGRESS_TRACKER_LOADED', true);

// ===============================
// DEPENDENCY CHECK (PHASE 17 ONLY)
// ===============================
if (
    !function_exists('phase17_build_player_skill_profile') ||
    !function_exists('phase17_build_player_tier_grade')
) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'  => 17,
            'engine' => 'cross-match-progress-tracker'
        ]);
    }
    return;
}

// ===============================
// CROSS-MATCH PROGRESS TRACKER
// ===============================

function phase17_track_cross_match_progress(array $matchSnapshots): array
{
    if (count($matchSnapshots) < 2) {
        return phase17_empty_progress_report();
    }

    usort($matchSnapshots, function ($a, $b) {
        return ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0);
    });

    $dimensionTrends = [];
    $tierTimeline    = [];
    $gradeTimeline   = [];

    for ($i = 1; $i < count($matchSnapshots); $i++) {
        $prev = $matchSnapshots[$i - 1];
        $curr = $matchSnapshots[$i];

        $prevDims = $prev['skill_profile']['dimensions'] ?? [];
        $currDims = $curr['skill_profile']['dimensions'] ?? [];

        foreach ($currDims as $dim => $value) {
            if (!array_key_exists($dim, $prevDims)) {
                continue;
            }

            $dimensionTrends[$dim][] = [
                'from_match' => $prev['match_id'] ?? ($i - 1),
                'to_match'   => $curr['match_id'] ?? $i,
                'delta'      => $value - $prevDims[$dim],
            ];
        }

        $tierTimeline[]  = $curr['tier_grade']['tier'] ?? null;
        $gradeTimeline[] = $curr['tier_grade']['grade'] ?? null;
    }

    return [
        'dimensions'  => phase17_build_dimension_trends($dimensionTrends),
        'tier_trend'  => phase17_build_simple_trend($tierTimeline),
        'grade_trend' => phase17_build_simple_trend($gradeTimeline),
        'meta' => [
            'phase'  => 17,
            'engine' => 'cross-match-progress-tracker',
            'stable' => true,
        ],
    ];
}

// ===============================
// TREND HELPERS
// ===============================

function phase17_build_dimension_trends(array $dimensionTrends): array
{
    $result = [];

    foreach ($dimensionTrends as $dim => $deltas) {
        $sum = 0.0;
        foreach ($deltas as $d) {
            $sum += $d['delta'];
        }

        $avgDelta = $sum / count($deltas);

        $result[$dim] = [
            'average_delta' => $avgDelta,
            'trend'   => phase17_map_trend($avgDelta),
            'samples' => count($deltas),
        ];
    }

    return $result;
}

function phase17_map_trend(float $delta): string
{
    if ($delta > 0.03) {
        return 'improving';
    }
    if ($delta < -0.03) {
        return 'declining';
    }
    return 'stable';
}

function phase17_build_simple_trend(array $timeline): array
{
    if (empty($timeline)) {
        return [
            'direction' => 'stable',
            'timeline'  => [],
        ];
    }

    return [
        'direction' => 'referential',
        'timeline'  => array_values($timeline),
    ];
}

function phase17_empty_progress_report(): array
{
    return [
        'dimensions' => [],
        'tier_trend' => [
            'direction' => 'stable',
            'timeline'  => [],
        ],
        'grade_trend' => [
            'direction' => 'stable',
            'timeline'  => [],
        ],
        'meta' => [
            'phase'  => 17,
            'engine' => 'cross-match-progress-tracker',
            'stable' => true,
        ],
    ];
}

// ===============================
// ENGINE READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('engine_loaded', [
        'phase'  => 17,
        'engine' => 'cross-match-progress-tracker'
    ]);
}

return;
