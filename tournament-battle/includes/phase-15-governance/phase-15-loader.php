<?php
/**
 * Phase 15 — Loader
 * File: /includes/phase-15-governance/phase-15-loader.php
 */

namespace TB\Phase15\Governance;

use TB\Phase15\Governance\Helpers\TB_P15_Idempotency;
use TB\Phase15\Governance\Helpers\TB_P15_Phase_Guard;
use TB\Phase15\Governance\Helpers\TB_P15_Context;
use TB\Phase15\Governance\Observers\TB_P15_Tournament_State_Observer;
use TB\Phase15\Governance\Observers\TB_P15_Payment_State_Observer;
use TB\Phase15\Governance\Observers\TB_P15_Referee_Decision_Observer;
use TB\Phase15\Governance\Auditors\TB_P15_State_Transition_Ledger;
use TB\Phase15\Governance\Auditors\TB_P15_Decision_Audit_Log;
use TB\Phase15\Governance\Recovery\TB_P15_Soft_Recovery_Rules;
use TB\Phase15\Governance\Recovery\TB_P15_Escalation_Triggers;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Loader
{
    private static bool $booted = false;

    /**
     * Boot Phase 15 in controlled order
     *
     * @return void
     */
    public static function boot(): void
    {
        if (self::$booted === true) {
            return;
        }

        if (
            !class_exists(TB_P15_Phase_Guard::class) ||
            !class_exists(TB_P15_Idempotency::class) ||
            !class_exists(TB_P15_Context::class)
        ) {
            return;
        }

        $guard = TB_P15_Phase_Guard::can_boot_phase();

        if (
            !is_array($guard) ||
            empty($guard['allowed']) ||
            $guard['allowed'] !== true
        ) {
            return;
        }

        /* Helpers (already loaded via autoload) */

        /* Observers */
        if (
            !class_exists(TB_P15_Tournament_State_Observer::class) ||
            !class_exists(TB_P15_Payment_State_Observer::class) ||
            !class_exists(TB_P15_Referee_Decision_Observer::class)
        ) {
            return;
        }

        TB_P15_Tournament_State_Observer::register();
        TB_P15_Payment_State_Observer::register();
        TB_P15_Referee_Decision_Observer::register();

        /* Auditors */
        if (
            !class_exists(TB_P15_State_Transition_Ledger::class) ||
            !class_exists(TB_P15_Decision_Audit_Log::class)
        ) {
            return;
        }

        TB_P15_State_Transition_Ledger::register();
        TB_P15_Decision_Audit_Log::register();

        /* Recovery */
        if (
            !class_exists(TB_P15_Soft_Recovery_Rules::class) ||
            !class_exists(TB_P15_Escalation_Triggers::class)
        ) {
            return;
        }

        TB_P15_Soft_Recovery_Rules::register();
        TB_P15_Escalation_Triggers::register();

        self::$booted = true;
    }
}
