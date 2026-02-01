<?php
/**
 * Phase 17 — AI Coaching / Skill Analytics Engine
 * File: /phase-17-ai-coaching/index.php
 *
 * PURPOSE (STRICT):
 * Master loader / orchestrator for Phase 17.
 * Sirf wiring, registration aur execution order define karta hai.
 *
 * ❌ No analytics
 * ❌ No scoring
 * ❌ No decisions
 * ❌ No data mutation
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
if (defined('PHASE_17_LOADED')) {
    return;
}
define('PHASE_17_LOADED', true);

// ===============================
// BASE PATH DEFINITIONS
// ===============================
define('PHASE_17_BASE', __DIR__);
define('PHASE_17_ENGINES', PHASE_17_BASE . '/engines');
define('PHASE_17_ANALYTICS', PHASE_17_BASE . '/analytics');
define('PHASE_17_COACHING', PHASE_17_BASE . '/coaching');
define('PHASE_17_INTEGRATION', PHASE_17_BASE . '/integration');
define('PHASE_17_CONTRACTS', PHASE_17_BASE . '/contracts');
define('PHASE_17_OBSERVABILITY', PHASE_17_BASE . '/observability');

// ===============================
// PHASE DEPENDENCY VALIDATION
// ===============================
$dependencyErrors = [];

// Phase 15 (read-only expectations)
if (!defined('PHASE_15_LOADED')) {
    $dependencyErrors[] = 'Phase 15 not loaded';
}

// Phase 16 (final authority)
if (!defined('PHASE_16_LOADED')) {
    $dependencyErrors[] = 'Phase 16 not loaded';
}

if (!empty($dependencyErrors)) {
    // Observability sirf Phase 17 ka use karegi
    if (file_exists(PHASE_17_OBSERVABILITY . '/logger.php')) {
        require_once PHASE_17_OBSERVABILITY . '/logger.php';
        if (function_exists('phase17_log')) {
            foreach ($dependencyErrors as $error) {
                phase17_log('dependency_error', [
                    'phase' => 17,
                    'message' => $error
                ]);
            }
        }
    }
    return; // clean abort, no partial execution
}

// ===============================
// CORE REGISTRATION (NO EXECUTION)
// ===============================

// Contracts (interfaces / schemas)
foreach (glob(PHASE_17_CONTRACTS . '/*.php') as $file) {
    require_once $file;
}

// Observability (logging, metrics hooks)
foreach (glob(PHASE_17_OBSERVABILITY . '/*.php') as $file) {
    require_once $file;
}

// Engines (core processors – registered only)
foreach (glob(PHASE_17_ENGINES . '/*.php') as $file) {
    require_once $file;
}

// Analytics modules (read-only consumers)
foreach (glob(PHASE_17_ANALYTICS . '/*.php') as $file) {
    require_once $file;
}

// Coaching logic (recommendation layers – no decisions here)
foreach (glob(PHASE_17_COACHING . '/*.php') as $file) {
    require_once $file;
}

// Integration adapters (Phase 15/16 bridges – read-only)
foreach (glob(PHASE_17_INTEGRATION . '/*.php') as $file) {
    require_once $file;
}

// ===============================
// EXECUTION ORDER DECLARATION
// ===============================
/*
 * Phase 17 Execution Contract:
 *
 * 1. Intake
 *    - Phase 15 timelines
 *    - Shot / action streams
 *
 * 2. Normalize
 *    - Shape enforcement via contracts
 *
 * 3. Dispatch
 *    - Analytics → Coaching pipelines
 *
 * Actual execution yahan nahi hoti.
 * Sirf order aur availability guarantee ki jati hai.
 */

// ===============================
// FINAL LOAD CONFIRMATION (SILENT)
// ===============================
if (function_exists('phase17_log')) {
    phase17_log('phase_loaded', [
        'phase' => 17,
        'status' => 'orchestrator_ready'
    ]);
}

return;
if (!defined('TB_PHASE_16_READY')) {
    define('TB_PHASE_16_READY', true);
}
