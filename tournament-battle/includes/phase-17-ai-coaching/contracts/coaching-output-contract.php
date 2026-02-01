<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/contracts/coaching-output-contract.php
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
if (defined('PHASE_17_COACHING_OUTPUT_CONTRACT_LOADED')) {
    return;
}
define('PHASE_17_COACHING_OUTPUT_CONTRACT_LOADED', true);

// ===============================
// COACHING OUTPUT CONTRACT (CANONICAL)
// ===============================

function phase17_get_coaching_output_contract(): array
{
    return [

        // ===========================
        // COACHING REPORT
        // ===========================
        'coaching_report' => [
            'overview' => [
                'tier'  => 'string',
                'grade' => 'string',
                'score' => 'float (0.0–1.0)',
            ],
            'strengths' => 'array',
            'improvement_areas' => 'array',
            'neutral_observations' => 'array',
            'raw_metrics' => [
                'skill_dimensions'  => 'array',
                'shot_quality'      => 'array',
                'decision_analysis' => 'array',
                'tempo_analysis'    => 'array',
            ],
        ],

        // ===========================
        // AI SUGGESTIONS
        // ===========================
        'ai_suggestions' => [
            'focus_areas' => [
                'area'        => 'string',
                'suggestion'  => 'string',
                'basis'       => 'array',
            ],
            'short_term_tips' => [
                'area'  => 'string',
                'tip'   => 'string',
                'basis' => 'array',
            ],
            'reinforcements' => [
                'area'    => 'string',
                'message' => 'string',
                'basis'   => 'array',
            ],
            'context' => [
                'tier'  => 'string',
                'grade' => 'string',
            ],
        ],

        // ===========================
        // SHARED METADATA
        // ===========================
        'meta' => [
            'phase'   => 'int',
            'purpose' => 'string',
            'stable'  => 'bool',
            'version' => 'string|null',
        ],
    ];
}

// ===============================
// CONTRACT READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('contract_loaded', [
        'phase'    => 17,
        'contract' => 'coaching-output-contract'
    ]);
}

return;
