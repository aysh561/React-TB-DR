<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/integration/anti-cheat-synergy-adapter.php
 *
 * PURPOSE (STRICT):
 * Phase 16 Advanced Anti-Cheat Framework ke FINAL aur AUTHORITATIVE
 * outputs ko Phase 17 ke liye READ-ONLY synergy adapter ki surat me expose karna.
 *
 * ❌ No verdict change
 * ❌ No enforcement
 * ❌ No recalculation
 * ❌ No mutation
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
if (defined('PHASE_17_AC_ADAPTER_LOADED')) {
    return;
}
define('PHASE_17_AC_ADAPTER_LOADED', true);

// ===============================
// DEPENDENCY CHECK (FINAL AUTHORITY)
// ===============================
if (!defined('PHASE_16_LOADED')) {
    phase17_log_safe('dependency_missing', 'PHASE_16_LOADED');
    return;
}

// ===============================
// ADAPTER: FINAL VERDICT ACCESSORS
// ===============================

/**
 * Final cheat verdict (authoritative, read-only)
 * @return array|null
 */
function phase17_get_anti_cheat_verdict(): ?array
{
    if (!function_exists('phase16_get_final_verdict')) {
        phase17_log_safe('contract_missing', 'final_verdict');
        return null;
    }

    $verdict = phase16_get_final_verdict();

    if (!is_array($verdict)) {
        return null;
    }

    return $verdict;
}

/**
 * Integrity flags (authoritative, read-only)
 * @return array
 */
function phase17_get_integrity_flags(): array
{
    if (!function_exists('phase16_get_integrity_flags')) {
        phase17_log_safe('contract_missing', 'integrity_flags');
        return [];
    }

    $flags = phase16_get_integrity_flags();

    if (!is_array($flags)) {
        return [];
    }

    return array_values($flags);
}

/**
 * Confidence / certainty scores (authoritative, read-only)
 * @return array|null
 */
function phase17_get_anti_cheat_confidence(): ?array
{
    if (!function_exists('phase16_get_confidence_scores')) {
        phase17_log_safe('contract_missing', 'confidence_scores');
        return null;
    }

    $scores = phase16_get_confidence_scores();

    if (!is_array($scores)) {
        return null;
    }

    return $scores;
}

// ===============================
// INTERNAL SAFE LOGGER (PHASE 17)
// ===============================
/**
 * Observability wrapper — hard dependency nahi
 */
function phase17_log_safe(string $type, string $context): void
{
    if (!function_exists('phase17_log')) {
        return;
    }

    phase17_log($type, [
        'phase'   => 17,
        'adapter' => 'anti-cheat-synergy',
        'context' => $context
    ]);
}

// ===============================
// ADAPTER READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('adapter_loaded', [
        'phase'   => 17,
        'adapter' => 'anti-cheat-synergy'
    ]);
}

return;
