<?php
/**
 * Phase 15 — Immutable Execution Context
 * File: /includes/phase-15-governance/helpers/class-tb-p15-context.php
 */

namespace TB\Phase15\Governance\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Context
{
    private const PHASE = 'phase_15';

    /**
     * Build immutable Phase-15 context
     *
     * @param array $args
     * @return array
     */
    public static function build(array $args): array
    {
        $context = [
            'phase'     => self::PHASE,
            'event'     => $args['event']     ?? null,
            'entity_id' => $args['entity_id'] ?? null,
            'timestamp' => $args['timestamp'] ?? time(),
            'source'    => $args['source']    ?? null,
        ];

        return self::normalize($context);
    }

    /**
     * Normalize context into deterministic shape
     *
     * @param array $context
     * @return array
     */
    public static function normalize(array $context): array
    {
        $normalized = [
            'phase'     => self::PHASE,
            'event'     => isset($context['event']) ? (string) $context['event'] : null,
            'entity_id' => isset($context['entity_id']) ? (string) $context['entity_id'] : null,
            'timestamp' => isset($context['timestamp']) && is_numeric($context['timestamp'])
                ? (int) $context['timestamp']
                : null,
            'source'    => isset($context['source']) ? (string) $context['source'] : null,
        ];

        ksort($normalized);

        return $normalized;
    }

    /**
     * Get phase identifier
     *
     * @return string
     */
    public static function get_phase(): string
    {
        return self::PHASE;
    }
}
