<?php
/**
 * Phase 15 — State Transition Ledger
 * File: /includes/phase-15-governance/auditors/class-tb-p15-state-transition-ledger.php
 *
 * STORAGE BOUNDARY:
 * - Phase-15 only
 * - Forensic / audit-grade ledger
 * - Append-only, immutable
 * - Not for high-frequency runtime analytics
 */

namespace TB\Phase15\Governance\Auditors;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_State_Transition_Ledger
{
    private const OPTION_PREFIX = 'tb_p15_ledger_';
    private const PHASE         = 'phase_15';

    /**
     * Register explicit Phase-15 signal listeners
     *
     * @return void
     */
    public static function register(): void
    {
        /* Tournament signals */
        add_action('tb_p15_tournament_created',        [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_tournament_join_closed',    [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_tournament_started',        [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_tournament_completed',      [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_tournament_cancelled',      [__CLASS__, 'capture'], 10, 1);

        /* Payment signals */
        add_action('tb_p15_payment_initiated',         [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_payment_submitted',         [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_payment_under_review',      [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_payment_verified',          [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_payment_rejected',          [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_payment_failed',            [__CLASS__, 'capture'], 10, 1);

        /* Referee signals */
        add_action('tb_p15_referee_referee_decision_recorded',   [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_referee_referee_decision_finalized',  [__CLASS__, 'capture'], 10, 1);
        add_action('tb_p15_referee_referee_decision_overturned', [__CLASS__, 'capture'], 10, 1);
    }

    /**
     * Append immutable ledger entry
     *
     * @param mixed $context
     * @return void
     */
    public static function capture($context): void
    {
        if (!is_array($context)) {
            return;
        }

        if (
            empty($context['phase']) ||
            empty($context['event']) ||
            empty($context['entity_id']) ||
            empty($context['timestamp']) ||
            empty($context['source'])
        ) {
            return;
        }

        if ($context['phase'] !== self::PHASE) {
            return;
        }

        $context_hash = hash('sha256', wp_json_encode($context));
        $option_key   = self::OPTION_PREFIX . $context_hash;

        $record = [
            'phase'        => self::PHASE,
            'event'        => (string) $context['event'],
            'entity_id'    => (string) $context['entity_id'],
            'timestamp'    => (int) $context['timestamp'],
            'source'       => (string) $context['source'],
            'context_hash' => $context_hash,
        ];

        add_option($option_key, $record, '', false);
    }
}
