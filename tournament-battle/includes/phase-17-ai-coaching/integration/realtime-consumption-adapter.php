<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/integration/realtime-consumption-adapter.php
 *
 * PURPOSE (STRICT):
 * Phase 15 Real-Time Engine ke outputs ko
 * Phase 17 ke liye READ-ONLY, normalized access layer me expose karna.
 *
 * ❌ No analytics
 * ❌ No scoring
 * ❌ No mutation
 * ❌ No execution orchestration
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
if (defined('PHASE_17_RT_ADAPTER_LOADED')) {
    return;
}
define('PHASE_17_RT_ADAPTER_LOADED', true);

// ===============================
// DEPENDENCY CHECK (READ-ONLY)
// ===============================
if (!defined('PHASE_15_LOADED')) {
    if (function_exists('phase17_log')) {
        phase17_log('dependency_missing', [
            'phase'   => 17,
            'adapter' => 'realtime-consumption',
            'missing' => 'PHASE_15_LOADED'
        ]);
    }
    return;
}

// ===============================
// ADAPTER: NORMALIZED ACCESSORS
// ===============================

/**
 * Match timeline (read-only)
 * @return array
 */
function phase17_get_match_timeline(): array
{
    if (!function_exists('phase15_get_match_timeline')) {
        phase17_log_safe('contract_missing', 'match_timeline');
        return [];
    }

    $timeline = phase15_get_match_timeline();

    if (!is_array($timeline)) {
        return [];
    }

    return array_values($timeline);
}

/**
 * Action / Shot stream (read-only)
 * @return array
 */
function phase17_get_action_stream(): array
{
    if (!function_exists('phase15_get_action_stream')) {
        phase17_log_safe('contract_missing', 'action_stream');
        return [];
    }

    $stream = phase15_get_action_stream();

    if (!is_array($stream)) {
        return [];
    }

    return array_values($stream);
}

/**
 * Temporal markers (read-only)
 * @return array
 */
function phase17_get_temporal_markers(): array
{
    if (!function_exists('phase15_get_temporal_markers')) {
        phase17_log_safe('contract_missing', 'temporal_markers');
        return [];
    }

    $markers = phase15_get_temporal_markers();

    if (!is_array($markers)) {
        return [];
    }

    return array_values($markers);
}

// ===============================
// INTERNAL SAFE LOGGER (PHASE 17)
// ===============================
/**
 * Observability wrapper — no hard dependency
 */
function phase17_log_safe(string $type, string $context): void
{
    if (!function_exists('phase17_log')) {
        return;
    }

    phase17_log($type, [
        'phase'   => 17,
        'adapter' => 'realtime-consumption',
        'context' => $context
    ]);
}

// ===============================
// ADAPTER READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('adapter_loaded', [
        'phase'   => 17,
        'adapter' => 'realtime-consumption'
    ]);
}

return;
