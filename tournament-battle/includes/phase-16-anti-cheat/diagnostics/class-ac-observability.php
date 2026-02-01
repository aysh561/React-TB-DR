<?php
/**
 * Phase 16 — Anti-Cheat Observability / Diagnostics
 * File: /includes/phase-16-anti-cheat/diagnostics/class-ac-observability.php
 *
 * ROLE (STRICT):
 * - Sirf non-intrusive observability
 * - Pipeline ke behavior ko read-only observe karna
 * - Koi decision, logic, scoring, ya control flow influence nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Observability
{
    /**
     * Capture diagnostic snapshot (non-blocking)
     *
     * @param array $payload Phase-16 loader ka final payload
     * @return void
     */
    public static function observe(array $payload): void
    {
        try {
            $snapshot = [
                'evidence_keys' => array_keys($payload),
                'signals' => [
                    'synthetic'  => self::countSignals($payload, 'synthetic'),
                    'behavioral' => self::countSignals($payload, 'behavioral'),
                    'cross_shot' => self::countSignals($payload, 'cross_shot'),
                ],
                'fusion' => [
                    'total_score' => $payload['fusion']['total_score'] ?? null,
                ],
                'decision' => [
                    'state' => $payload['decision']['state'] ?? null,
                ],
                'timestamp' => gmdate('c'),
            ];

            /**
             * Pluggable diagnostics hook
             * Expected callable: function(array $snapshot): void
             */
            $observer = apply_filters('ac_observability_handler', null);

            if (is_callable($observer)) {
                $observer($snapshot);
            }
        } catch (Throwable $e) {
            // ABSOLUTELY SILENT FAILURE
            // Observability kabhi bhi pipeline ko affect nahi kare gi
        }
    }

    /**
     * Safe signal counter
     *
     * @param array $payload
     * @param string $key
     * @return int
     */
    private static function countSignals(array $payload, string $key): int
    {
        if (
            !isset($payload['signals'][$key]) ||
            !is_array($payload['signals'][$key])
        ) {
            return 0;
        }

        return count($payload['signals'][$key]);
    }
}
