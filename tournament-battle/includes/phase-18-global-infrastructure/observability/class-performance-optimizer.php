<?php
declare(strict_types=1);

namespace TB\Phase18\Observability;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 20 / 25 — Global Performance Snapshot (Read-Only Diagnostics)
 */
final class PerformanceOptimizer
{
    /**
     * Observe and normalize performance payload (read-only, stateless)
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function observe(array $payload): array
    {
        if (
            !isset(
                $payload['metric'],
                $payload['value'],
                $payload['timestamp']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid performance payload structure'
            );
        }

        if (!\is_string($payload['metric']) || $payload['metric'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: metric must be non-empty string'
            );
        }

        if (!\is_int($payload['value']) && !\is_float($payload['value'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: value must be int or float'
            );
        }

        if (!\is_int($payload['timestamp'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: timestamp must be int'
            );
        }

        return [
            'metric'    => $payload['metric'],
            'value'     => $payload['value'],
            'timestamp' => $payload['timestamp'],
        ];
    }
}
