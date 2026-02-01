<?php
/**
 * Phase 15 — Soft Recovery Rules
 * File: /includes/phase-15-governance/recovery/class-tb-p15-soft-recovery-rules.php
 */

namespace TB\Phase15\Governance\Recovery;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Soft_Recovery_Rules
{
    private const PHASE = 'phase_15';

    /**
     * Register Phase-15 advisory listeners
     *
     * @return void
     */
    public static function register(): void
    {
        add_action('tb_p15_tournament_created',        [__CLASS__, 'observe'], 10, 1);
        add_action('tb_p15_tournament_started',        [__CLASS__, 'observe'], 10, 1);
        add_action('tb_p15_tournament_completed',      [__CLASS__, 'observe'], 10, 1);
        add_action('tb_p15_tournament_cancelled',      [__CLASS__, 'observe'], 10, 1);

        add_action('tb_p15_payment_verified',          [__CLASS__, 'observe'], 10, 1);
        add_action('tb_p15_payment_rejected',          [__CLASS__, 'observe'], 10, 1);
        add_action('tb_p15_payment_failed',            [__CLASS__, 'observe'], 10, 1);

        add_action('tb_p15_referee_referee_decision_finalized',  [__CLASS__, 'observe'], 10, 1);
        add_action('tb_p15_referee_referee_decision_overturned', [__CLASS__, 'observe'], 10, 1);
    }

    /**
     * Validate incoming context and trigger evaluation
     *
     * @param mixed $context
     * @return void
     */
    public static function observe($context): void
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

        $hints = self::evaluate($context);

        if (empty($hints)) {
            return;
        }

        self::emit($context, $hints);
    }

    /**
     * Derive soft recovery hints (advisory only)
     *
     * @param array $context
     * @return array
     */
    private static function evaluate(array $context): array
    {
        $hints = [];

        if ($context['event'] === 'tournament_completed' && strpos((string) $context['source'], 'payment') !== false) {
            $hints[] = [
                'severity' => 'medium',
                'message'  => 'Tournament completed ke sath payment-related source detect hua. Manual verification suggest ki jati hai.',
            ];
        }

        if ($context['event'] === 'payment_failed') {
            $hints[] = [
                'severity' => 'low',
                'message'  => 'Payment failure detect hui. Agar repeat ho to manual review consider karein.',
            ];
        }

        if ($context['event'] === 'referee_decision_overturned') {
            $hints[] = [
                'severity' => 'high',
                'message'  => 'Referee decision overturn hui. Related tournament aur payment outcomes ka audit recommend hai.',
            ];
        }

        return $hints;
    }

    /**
     * Emit standardized recovery hints
     *
     * @param array $context
     * @param array $hints
     * @return void
     */
    private static function emit(array $context, array $hints): void
    {
        foreach ($hints as $hint) {
            if (
                empty($hint['severity']) ||
                empty($hint['message'])
            ) {
                continue;
            }

            do_action('tb_p15_recovery_hint', [
                'phase'     => self::PHASE,
                'event'     => (string) $context['event'],
                'entity_id' => (string) $context['entity_id'],
                'severity'  => (string) $hint['severity'],
                'message'   => (string) $hint['message'],
                'source'    => 'recovery:soft_rules',
            ]);
        }
    }
}
