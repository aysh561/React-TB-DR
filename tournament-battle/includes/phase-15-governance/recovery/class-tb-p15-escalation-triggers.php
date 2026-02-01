<?php
/**
 * Phase 15 — Escalation Triggers
 * File: /includes/phase-15-governance/recovery/class-tb-p15-escalation-triggers.php
 */

namespace TB\Phase15\Governance\Recovery;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Escalation_Triggers
{
    private const PHASE = 'phase_15';

    /**
     * In-memory trackers (request-scope only)
     */
    private static array $mediumCounters   = [];
    private static array $paymentFailures  = [];
    private static array $escalatedEntities = [];

    /**
     * Register recovery hint listener
     *
     * @return void
     */
    public static function register(): void
    {
        add_action('tb_p15_recovery_hint', [__CLASS__, 'observe'], 10, 1);
    }

    /**
     * Observe recovery hints and decide escalation
     *
     * @param mixed $hint
     * @return void
     */
    public static function observe($hint): void
    {
        if (!is_array($hint)) {
            return;
        }

        if (
            empty($hint['phase']) ||
            empty($hint['event']) ||
            empty($hint['entity_id']) ||
            empty($hint['severity']) ||
            empty($hint['message']) ||
            empty($hint['source'])
        ) {
            return;
        }

        if ($hint['phase'] !== self::PHASE) {
            return;
        }

        $entity_id = (string) $hint['entity_id'];

        if (isset(self::$escalatedEntities[$entity_id])) {
            return;
        }

        if ($hint['severity'] === 'high') {
            self::emit_escalation(
                $entity_id,
                'High severity recovery hint received.',
                [$hint['event']]
            );
            return;
        }

        if ($hint['severity'] === 'medium') {
            self::$mediumCounters[$entity_id] = (self::$mediumCounters[$entity_id] ?? 0) + 1;

            if (self::$mediumCounters[$entity_id] >= 2) {
                self::emit_escalation(
                    $entity_id,
                    'Multiple medium severity recovery hints detected for same entity.',
                    [$hint['event']]
                );
                return;
            }
        }

        if ($hint['event'] === 'payment_failed') {
            self::$paymentFailures[$entity_id] = (self::$paymentFailures[$entity_id] ?? 0) + 1;

            if (self::$paymentFailures[$entity_id] >= 2) {
                self::emit_escalation(
                    $entity_id,
                    'Repeated payment failure recovery hints detected.',
                    [$hint['event']]
                );
                return;
            }
        }
    }

    /**
     * Emit escalation signal
     *
     * @param string $entity_id
     * @param string $reason
     * @param array  $triggered_by
     * @return void
     */
    private static function emit_escalation(string $entity_id, string $reason, array $triggered_by): void
    {
        if ($entity_id === '' || $reason === '') {
            return;
        }

        self::$escalatedEntities[$entity_id] = true;

        do_action('tb_p15_escalation_required', [
            'phase'        => self::PHASE,
            'entity_id'    => $entity_id,
            'severity'     => 'high',
            'reason'       => $reason,
            'source'       => 'recovery:escalation',
            'triggered_by' => array_values($triggered_by),
        ]);
    }
}
