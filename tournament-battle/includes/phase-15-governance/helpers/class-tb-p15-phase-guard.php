<?php
/**
 * Phase 15 — Phase Guard
 * File: /includes/phase-15-governance/helpers/class-tb-p15-phase-guard.php
 */

namespace TB\Phase15\Governance\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Phase_Guard
{
    private const PHASE_14_COMPLETION_MARKER = 'tb_phase_14_completed';
    private const PHASE_15_BOOT_MARKER       = 'tb_phase_15_booted';

    /**
     * Verify ke Phase 14 fully completed hai
     *
     * @return array
     */
    public static function verify_previous_phase(): array
    {
        if (!defined('TB_PHASE_14_COMPLETED')) {
            $marker = get_option(self::PHASE_14_COMPLETION_MARKER, null);

            if ($marker !== true) {
                return [
                    'status'  => 'blocked',
                    'reason'  => 'phase_14_incomplete',
                    'allowed' => false,
                ];
            }
        } elseif (TB_PHASE_14_COMPLETED !== true) {
            return [
                'status'  => 'blocked',
                'reason'  => 'phase_14_flag_invalid',
                'allowed' => false,
            ];
        }

        return [
            'status'  => 'ok',
            'reason'  => 'phase_14_verified',
            'allowed' => true,
        ];
    }

    /**
     * Acquire single boot lock for Phase 15
     *
     * @return array
     */
    public static function acquire_phase_lock(): array
    {
        $existing = get_option(self::PHASE_15_BOOT_MARKER, null);

        if ($existing !== null) {
            return [
                'status'  => 'blocked',
                'reason'  => 'phase_15_already_booted',
                'allowed' => false,
            ];
        }

        $added = add_option(self::PHASE_15_BOOT_MARKER, true, '', false);

        if (!$added) {
            return [
                'status'  => 'blocked',
                'reason'  => 'phase_15_lock_race_condition',
                'allowed' => false,
            ];
        }

        return [
            'status'  => 'locked',
            'reason'  => 'phase_15_boot_acquired',
            'allowed' => true,
        ];
    }

    /**
     * Final gate check to decide Phase 15 boot
     *
     * @return array
     */
    public static function can_boot_phase(): array
    {
        $phase14 = self::verify_previous_phase();

        if ($phase14['allowed'] !== true) {
            return [
                'status'  => 'blocked',
                'reason'  => 'previous_phase_failed',
                'allowed' => false,
            ];
        }

        $lock = self::acquire_phase_lock();

        if ($lock['allowed'] !== true) {
            return [
                'status'  => 'blocked',
                'reason'  => 'duplicate_phase_boot',
                'allowed' => false,
            ];
        }

        return [
            'status'  => 'allowed',
            'reason'  => 'phase_15_can_boot',
            'allowed' => true,
        ];
    }
}
