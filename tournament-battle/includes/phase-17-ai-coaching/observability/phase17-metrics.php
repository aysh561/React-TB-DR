<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/observability/phase17-metrics.php
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
if (defined('PHASE_17_METRICS_REGISTRY_LOADED')) {
    return;
}
define('PHASE_17_METRICS_REGISTRY_LOADED', true);

// ===============================
// IN-MEMORY METRICS STORE
// ===============================

$GLOBALS['PHASE_17_METRICS'] = $GLOBALS['PHASE_17_METRICS'] ?? [
    'engine_loads'     => [],
    'engine_invokes'   => [],
    'analytics_invokes'=> [],
    'coaching_invokes' => [],
];

// ===============================
// METRICS REGISTRATION HELPERS
// ===============================

function phase17_register_engine(string $engine): void
{
    if (!isset($GLOBALS['PHASE_17_METRICS']['engine_loads'][$engine])) {
        $GLOBALS['PHASE_17_METRICS']['engine_loads'][$engine] = 0;
    }
}

function phase17_increment_engine_load(string $engine): void
{
    phase17_register_engine($engine);
    $GLOBALS['PHASE_17_METRICS']['engine_loads'][$engine]++;
}

function phase17_increment_engine_invoke(string $engine): void
{
    if (!isset($GLOBALS['PHASE_17_METRICS']['engine_invokes'][$engine])) {
        $GLOBALS['PHASE_17_METRICS']['engine_invokes'][$engine] = 0;
    }
    $GLOBALS['PHASE_17_METRICS']['engine_invokes'][$engine]++;
}

function phase17_increment_analytics_invoke(string $analytics): void
{
    if (!isset($GLOBALS['PHASE_17_METRICS']['analytics_invokes'][$analytics])) {
        $GLOBALS['PHASE_17_METRICS']['analytics_invokes'][$analytics] = 0;
    }
    $GLOBALS['PHASE_17_METRICS']['analytics_invokes'][$analytics]++;
}

function phase17_increment_coaching_invoke(string $module): void
{
    if (!isset($GLOBALS['PHASE_17_METRICS']['coaching_invokes'][$module])) {
        $GLOBALS['PHASE_17_METRICS']['coaching_invokes'][$module] = 0;
    }
    $GLOBALS['PHASE_17_METRICS']['coaching_invokes'][$module]++;
}

// ===============================
// METRICS READ HELPERS (READ-ONLY)
// ===============================

function phase17_get_metrics_snapshot(): array
{
    return [
        'engine_loads'      => $GLOBALS['PHASE_17_METRICS']['engine_loads'],
        'engine_invokes'    => $GLOBALS['PHASE_17_METRICS']['engine_invokes'],
        'analytics_invokes' => $GLOBALS['PHASE_17_METRICS']['analytics_invokes'],
        'coaching_invokes'  => $GLOBALS['PHASE_17_METRICS']['coaching_invokes'],
        'meta' => [
            'phase'  => 17,
            'stable' => true,
        ],
    ];
}

// ===============================
// METRICS REGISTRY READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('observability_loaded', [
        'phase' => 17,
        'unit'  => 'metrics-registry'
    ]);
}

return;
