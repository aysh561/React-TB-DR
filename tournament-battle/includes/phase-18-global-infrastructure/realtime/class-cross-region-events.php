<?php
declare(strict_types=1);

namespace TB\Phase18\Realtime;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 8 / 25 — Cross-Region Events (Read-Only)
 */
final class CrossRegionEvents
{
    /**
     * Per-region observed event counters
     *
     * @var array<string,int>
     */
    private static array $regionEventCount = [];

    /**
     * Last observed event timestamp (global)
     *
     * @var int|null
     */
    private static ?int $lastEventTimestamp = null;

    /**
     * Observe cross-region realtime event (read-only)
     *
     * @param array<string,mixed> $payload
     */
    public static function observe(array $payload): void
    {
        if (
            !isset(
                $payload['event_id'],
                $payload['region_id'],
                $payload['timestamp'],
                $payload['type']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid cross-region event payload structure'
            );
        }

        if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: region_id must be non-empty string'
            );
        }

        if (!\is_int($payload['timestamp'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: timestamp must be int'
            );
        }

        if (!\is_string($payload['type'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: type must be string'
            );
        }

        if (!isset(self::$regionEventCount[$payload['region_id']])) {
            self::$regionEventCount[$payload['region_id']] = 0;
        }

        self::$regionEventCount[$payload['region_id']]++;
        self::$lastEventTimestamp = $payload['timestamp'];
    }

    /**
     * Health snapshot (read-only diagnostics)
     *
     * @return array<string,mixed>
     */
    public static function getHealthSnapshot(): array
    {
        return [
            'per_region_events'   => self::$regionEventCount,
            'last_event_timestamp'=> self::$lastEventTimestamp,
        ];
    }
}
