<?php
declare(strict_types=1);

namespace TB\Phase18\Observability;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 19 / 25 — Global Observability Layer (Read-Only Diagnostics)
 */
final class GlobalLogging
{
    /**
     * Observe and normalize diagnostic payload (read-only, stateless)
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function observe(array $payload): array
    {
        if (
            !isset(
                $payload['event'],
                $payload['timestamp'],
                $payload['source']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid diagnostic payload structure'
            );
        }

        if (!\is_string($payload['event']) || $payload['event'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: event must be non-empty string'
            );
        }

        if (!\is_int($payload['timestamp'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: timestamp must be int'
            );
        }

        if (!\is_string($payload['source']) || $payload['source'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: source must be non-empty string'
            );
        }

        return [
            'event'     => $payload['event'],
            'timestamp' => $payload['timestamp'],
            'source'    => $payload['source'],
        ];
    }
}
