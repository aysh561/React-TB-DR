<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/contracts/skill-metrics-schema.php
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
if (defined('PHASE_17_SKILL_METRICS_SCHEMA_LOADED')) {
    return;
}
define('PHASE_17_SKILL_METRICS_SCHEMA_LOADED', true);

// ===============================
// SKILL METRICS SCHEMA (CANONICAL)
// ===============================

function phase17_get_skill_metrics_schema(): array
{
    return [

        // ===========================
        // CORE SKILL PROFILE
        // ===========================
        'skill_profile' => [
            'dimensions' => [
                'consistency'      => 'float (0.0–1.0)',
                'accuracy'         => 'float (0.0–1.0)',
                'decision_quality' => 'float (0.0–1.0)',
                'tempo_control'    => 'float (0.0–1.0)',
            ],
            'integrity_context' => [
                'verdict'    => 'array|null',
                'flags'      => 'array',
                'confidence' => 'array|null',
            ],
        ],

        // ===========================
        // SHOT QUALITY METRICS
        // ===========================
        'shot_quality' => [
            'shot_index' => 'int',
            'precision'  => 'float (0.0–1.0)',
            'timing'     => 'float (0.0–1.0)',
            'control'    => 'float (0.0–1.0)',
            'alignment'  => 'float (0.0–1.0)',
        ],

        // ===========================
        // DECISION ANALYSIS METRICS
        // ===========================
        'decision_analysis' => [
            'decisions' => [
                'decision_index' => 'int',
                'risk_reward'    => 'float (0.0–1.0)',
                'context_awareness' => 'float (0.0–1.0)',
                'consistency'    => 'float (0.0–1.0)',
                'optimality'     => 'float (0.0–1.0)',
            ],
            'summary' => [
                'total_decisions'      => 'int',
                'avg_risk_reward'      => 'float',
                'avg_context_awareness'=> 'float',
                'avg_consistency'      => 'float',
                'optimal_ratio'        => 'float',
            ],
        ],

        // ===========================
        // TEMPO & BEHAVIOR METRICS
        // ===========================
        'tempo_analysis' => [
            'segments' => [
                'segment_index' => 'int',
                'frequency'     => 'float (0.0–1.0)',
                'rhythm'        => 'float (0.0–1.0)',
                'burst_idle'    => 'float (0.0–1.0)',
                'pressure'      => 'float (0.0–1.0)',
            ],
            'summary' => [
                'segments'       => 'int',
                'avg_frequency'  => 'float',
                'avg_rhythm'     => 'float',
                'avg_burst_idle' => 'float',
                'avg_pressure'   => 'float',
            ],
        ],

        // ===========================
        // TIER & GRADE
        // ===========================
        'tier_grade' => [
            'tier'  => 'string',
            'grade' => 'string',
            'score' => 'float (0.0–1.0)',
        ],

        // ===========================
        // REPUTATION METRICS
        // ===========================
        'reputation_metrics' => [
            'consistency_reputation'  => 'float (0.0–1.0)',
            'improvement_trajectory'  => 'float (0.0–1.0)',
            'stability_volatility'    => 'float (0.0–1.0)',
            'coaching_responsiveness' => 'float (0.0–1.0)',
        ],

        // ===========================
        // COACHING REPORT
        // ===========================
        'coaching_report' => [
            'overview' => [
                'tier'  => 'string',
                'grade' => 'string',
                'score' => 'float',
            ],
            'strengths' => 'array',
            'improvement_areas' => 'array',
            'neutral_observations' => 'array',
            'raw_metrics' => 'array',
        ],

        // ===========================
        // AI SUGGESTIONS
        // ===========================
        'ai_suggestions' => [
            'focus_areas'      => 'array',
            'short_term_tips'  => 'array',
            'reinforcements'   => 'array',
            'context' => [
                'tier'  => 'string',
                'grade' => 'string',
            ],
        ],
    ];
}

// ===============================
// SCHEMA READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('contract_loaded', [
        'phase'    => 17,
        'contract' => 'skill-metrics-schema'
    ]);
}

return;
