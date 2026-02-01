<?php
/**
 * Phase 16 — Anti-Cheat Decision Log (Governance)
 * File: /includes/phase-16-anti-cheat/governance/class-ac-decision-log.php
 *
 * ROLE (STRICT):
 * - Sirf decision logging
 * - Append-only, audit-safe snapshot
 * - Koi decision change, enforcement, ya re-processing nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Decision_Log
{
    /**
     * Log finalized decision snapshot
     *
     * @param array $evidence Decision engine ka output
     * @return array Same payload, optionally with governance.log_id
     *
     * @throws RuntimeException
     */
    public static function log(array $evidence): array
    {
        if (
            !isset($evidence['decision']) ||
            !is_array($evidence['decision']) ||
            !isset(
                $evidence['decision']['state'],
                $evidence['decision']['score'],
                $evidence['decision']['reason'],
                $evidence['decision']['breakdown']
            )
        ) {
            throw new RuntimeException('Decision data missing for governance logging');
        }

        $snapshot = [
            'state'     => (string)$evidence['decision']['state'],
            'score'     => (float)$evidence['decision']['score'],
            'reason'    => (string)$evidence['decision']['reason'],
            'breakdown' => $evidence['decision']['breakdown'],
            'timestamp' => gmdate('c'), // UTC ISO-8601
        ];

        /**
         * Storage abstraction:
         * Logger callable bahar se inject hoga
         * Expected: function(array $snapshot): string $logId
         */
        $logger = apply_filters('ac_decision_logger', null);

        if (!is_callable($logger)) {
            throw new RuntimeException('No valid decision logger configured');
        }

        $logId = $logger($snapshot);

        if (!is_string($logId) || trim($logId) === '') {
            throw new RuntimeException('Decision logging failed');
        }

        // Immutable output — decision intact
        $output = $evidence;
        $output['governance']['log_id'] = $logId;

        return $output;
    }
}
