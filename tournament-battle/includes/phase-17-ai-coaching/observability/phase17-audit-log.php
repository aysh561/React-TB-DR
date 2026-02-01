<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/observability/phase17-audit-log.php
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
if (defined('PHASE_17_AUDIT_LOG_LOADED')) {
    return;
}
define('PHASE_17_AUDIT_LOG_LOADED', true);

// ===============================
// IN-MEMORY AUDIT STORE (APPEND-ONLY)
// ===============================
$GLOBALS['PHASE_17_AUDIT_LOG'] = $GLOBALS['PHASE_17_AUDIT_LOG'] ?? [];

// ===============================
// AUDIT LOG HELPERS
// ===============================

/**
 * Append-only audit event
 */
function phase17_audit_log_event(string $event, array $context = []): void
{
    $GLOBALS['PHASE_17_AUDIT_LOG'][] = [
        'timestamp' => time(),
        'phase'     => 17,
        'event'     => $event,
        'context'   => $context,
    ];
}

/**
 * Read-only audit log snapshot
 */
function phase17_get_audit_log(): array
{
    return [
        'events' => $GLOBALS['PHASE_17_AUDIT_LOG'],
        'meta' => [
            'phase'  => 17,
            'stable' => true,
        ],
    ];
}

// ===============================
// AUDIT LOG READY (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('observability_loaded', [
        'phase' => 17,
        'unit'  => 'audit-log'
    ]);
}

return;
