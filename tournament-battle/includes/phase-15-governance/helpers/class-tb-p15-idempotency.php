<?php
/**
 * Phase 15 — Central Idempotency Authority
 * File: /includes/phase-15-governance/helpers/class-tb-p15-idempotency.php
 *
 * ENGINE COMPLIANT — FINAL
 */

namespace TB\Phase15\Governance\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Idempotency
{
    /**
     * WordPress option prefix (append-safe)
     */
    private const OPTION_PREFIX = 'tb_p15_idem_';

    /**
     * Phase identifier
     */
    private const PHASE = 'phase_15';

    /**
     * Generate deterministic idempotency key
     *
     * @param string     $event
     * @param string|int $entity_id
     * @param array      $context
     * @return string
     */
    public static function generate_key(string $event, $entity_id, array $context = []): string
    {
        $context = self::canonicalize_context($context);

        $payload = [
            'phase'   => self::PHASE,
            'event'   => $event,
            'entity'  => (string) $entity_id,
            'context' => $context,
        ];

        return hash('sha256', wp_json_encode($payload));
    }

    /**
     * Acquire idempotent execution authority
     *
     * @param string     $event
     * @param string|int $entity_id
     * @param array      $context
     * @return array {
     *   @type bool   $allowed
     *   @type string $key
     *   @type string $status   new|duplicate
     *   @type int    $timestamp
     * }
     */
    public static function acquire(string $event, $entity_id, array $context = []): array
    {
        $key       = self::generate_key($event, $entity_id, $context);
        $optionKey = self::OPTION_PREFIX . $key;

        $existing = get_option($optionKey, null);

        if ($existing !== null) {
            return [
                'allowed'   => false,
                'key'       => $key,
                'status'    => 'duplicate',
                'timestamp' => (int) ($existing['timestamp'] ?? 0),
            ];
        }

        $record = [
            'event'     => $event,
            'entity'    => (string) $entity_id,
            'phase'     => self::PHASE,
            'timestamp' => time(),
        ];

        /**
         * add_option is append-safe:
         * agar key already exist ho to silently fail karega
         */
        $added = add_option($optionKey, $record, '', false);

        if (!$added) {
            $fallback = get_option($optionKey, []);

            return [
                'allowed'   => false,
                'key'       => $key,
                'status'    => 'duplicate',
                'timestamp' => (int) ($fallback['timestamp'] ?? 0),
            ];
        }

        return [
            'allowed'   => true,
            'key'       => $key,
            'status'    => 'new',
            'timestamp' => $record['timestamp'],
        ];
    }

    /**
     * Canonicalize context array for deterministic hashing
     *
     * @param array $context
     * @return array
     */
    private static function canonicalize_context(array $context): array
    {
        foreach ($context as $k => $v) {
            if (is_array($v)) {
                $context[$k] = self::canonicalize_context($v);
            }
        }

        ksort($context);
        return $context;
    }
}
