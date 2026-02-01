<?php
declare(strict_types=1);

namespace TB\Phase18\Anticheat;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 16 / 25 — Global Anti-Cheat Cluster (Read-Only Consumer)
 */
final class GlobalAnticheatCluster
{
    /**
     * Consume and normalize anti-cheat signal payload (read-only, stateless)
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function observe(array $payload): array
    {
        if (
            !isset(
                $payload['identity_id'],
                $payload['signal_type'],
                $payload['timestamp']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid anti-cheat payload structure'
            );
        }

        if (!\is_string($payload['identity_id']) || $payload['identity_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: identity_id must be non-empty string'
            );
        }

        if (!\is_string($payload['signal_type']) || $payload['signal_type'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: signal_type must be non-empty string'
            );
        }

        if (!\is_int($payload['timestamp'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: timestamp must be int'
            );
        }

        return [
            'identity_id' => $payload['identity_id'],
            'signal_type' => $payload['signal_type'],
            'timestamp'   => $payload['timestamp'],
        ];
    }
}
