<?php
declare(strict_types=1);

namespace TB\Phase18\Spectators;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 17 / 25 — Global Spectator Network (Read-Only Observer)
 */
final class GlobalSpectatorNetwork
{
    /**
     * Observe and normalize spectator event payload (read-only, stateless)
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function observe(array $payload): array
    {
        if (
            !isset(
                $payload['tournament_id'],
                $payload['region_id'],
                $payload['event_type'],
                $payload['timestamp']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid spectator payload structure'
            );
        }

        if (!\is_string($payload['tournament_id']) || $payload['tournament_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: tournament_id must be non-empty string'
            );
        }

        if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: region_id must be non-empty string'
            );
        }

        if (!\is_string($payload['event_type']) || $payload['event_type'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: event_type must be non-empty string'
            );
        }

        if (!\is_int($payload['timestamp'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: timestamp must be int'
            );
        }

        return [
            'tournament_id' => $payload['tournament_id'],
            'region_id'     => $payload['region_id'],
            'event_type'    => $payload['event_type'],
            'timestamp'     => $payload['timestamp'],
        ];
    }
}
