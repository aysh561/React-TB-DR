<?php
/**
 * Phase 16 — Anti-Cheat Loader
 * File: /includes/phase-16-anti-cheat/phase-16-anti-cheat-loader.php
 *
 * ROLE (STRICT):
 * Sirf loader / orchestrator.
 * Koi detection, scoring, decision, ya business logic nahi.
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Load Contracts
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/contracts/class-ac-evidence-contract.php';
require_once __DIR__ . '/contracts/class-ac-decision-contract.php';


/*
|--------------------------------------------------------------------------
| Load Modules (Registration Only)
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/intake/class-ac-evidence-normalizer.php';
require_once __DIR__ . '/intake/class-ac-metadata-validator.php';
require_once __DIR__ . '/verification/class-ac-duplicate-check.php';
require_once __DIR__ . '/verification/class-ac-scene-check.php';
require_once __DIR__ . '/verification/class-ac-temporal-check.php';
require_once __DIR__ . '/intelligence/class-ac-behavior-analyzer.php';
require_once __DIR__ . '/intelligence/class-ac-cross-shot-engine.php';
require_once __DIR__ . '/intelligence/class-ac-synthetic-detector.php';
require_once __DIR__ . '/fusion/class-ac-decision-engine.php';
require_once __DIR__ . '/fusion/class-ac-signal-weighting.php';
require_once __DIR__ . '/governance/class-ac-appeal-bundle.php';
require_once __DIR__ . '/governance/class-ac-decision-log.php';
require_once __DIR__ . '/diagnostics/class-ac-observability.php';

/*
|--------------------------------------------------------------------------
| Phase 16 Entry Point
|--------------------------------------------------------------------------
| Phase 15 Real-Time Engine yahan payload pass karega.
*/
function phase16_anti_cheat_loader(array $payload)
{
    /*
    |--------------------------------------------------------------
    | FIX 1 — Fail-Fast Input Contract Enforcement
    |--------------------------------------------------------------
    | validate() fail par exception bubble hogi
    | loader koi fallback / default output generate nahi karega
    */
    $input = AntiCheatInputContract::validate($payload);

    /*
    |--------------------------------------------------------------
    | FIX 2 — Immutable Pipeline Bootstrap (Hard Order)
    | Intake → Verification → Intelligence → Fusion → Governance
    |--------------------------------------------------------------
    | Sirf variable reassignment
    | Koi in-place mutation nahi
    */
    $stageIntake       = Phase16_Intake::handle($input);
    $stageVerification = Phase16_Verification::handle($stageIntake);
    $stageIntelligence = Phase16_Intelligence::handle($stageVerification);
    $stageFusion       = Phase16_Fusion::handle($stageIntelligence);
    $finalResult       = Phase16_Governance::handle($stageFusion);

    /*
    |--------------------------------------------------------------
    | FIX 3 — Governance Output Shape Enforcement
    |--------------------------------------------------------------
    | Scalar return forbidden
    | Output contract ke minimum keys required
    */
    if (
        !is_array($finalResult) &&
        !is_object($finalResult)
    ) {
        throw new RuntimeException('Phase16_Governance returned invalid output type');
    }

    /*
    |--------------------------------------------------------------
    | FIX 4 — Diagnostics Isolation (Hard Rule)
    |--------------------------------------------------------------
    | Loader try/catch use nahi karega
    | Diagnostics module ke andar suppression handle hogi
    */
    Phase16_Diagnostics::record($input, $finalResult);

    /*
    |--------------------------------------------------------------
    | FIX 5 — Single Exit Guarantee
    |--------------------------------------------------------------
    | Sirf ek hi return statement
    */
    return AntiCheatOutputContract::build($finalResult);
}
if (!defined('TB_PHASE_16_READY')) {
    define('TB_PHASE_16_READY', true);
}
