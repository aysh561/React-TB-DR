<?php
declare(strict_types=1);

namespace TB\Phase18\Realtime;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 7 / 25 — Global WebSocket Mesh (Read-Only)
 */
final class GlobalWebSocketMesh
{
    /**
     * Last observed realtime event timestamp
     *
     * @var int|null
     */
    private static ?int $lastEventTimestamp = null;

    /**
     * Total observed events counter
     *
     * @var int
     */
    private static int $eventCount = 0;

    /**
     * Accept realtime signal envelope (read-only)
     *
     * @param array<string,mixed> $payload
     */
    public static function observe(array $payload): void
    {
        if (!isset($payload['event_id'], $payload['timestamp'], $payload['type'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid realtime payload structure'
            );
        }

        if (!\is_int($payload['timestamp'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: realtime payload timestamp must be int'
            );
        }

        self::$lastEventTimestamp = $payload['timestamp'];
        self::$eventCount++;
    }

    /**
     * Health snapshot (read-only)
     *
     * @return array<string,mixed>
     */
    public static function getHealthSnapshot(): array
    {
        return [
            'last_event_timestamp' => self::$lastEventTimestamp,
            'observed_events'      => self::$eventCount,
        ];
    }
}
