<?php
/**
 * Phase 15 — Real-Time Engine
 * File 21/24 — Real-Time Observability Hooks
 * Path: /includes/phase-15-realtime/diagnostics/class-rt-observability.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Diagnostics;

final class RT_Observability
{
    /**
     * Process-local observability data
     */
    private static int $eventsCount   = 0;
    private static string $lastEvent  = '';
    private static int $lastEventTs   = 0;

    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Record an internal engine / security / sync event.
     */
    public static function record(string $event, array $context = []): void
    {
        try {
            if ($event === '') {
                return;
            }

            self::$eventsCount++;
            self::$lastEvent  = $event;
            self::$lastEventTs = time();

            // context intentionally ignored (read-only, no storage)

        } catch (\Throwable $e) {
            return;
        }
    }

    /**
     * Snapshot current observability metrics.
     *
     * @return array<string, int|string>
     */
    public static function snapshot(): array
    {
        try {
            return [
                'events_count'  => self::$eventsCount,
                'last_event'    => self::$lastEvent,
                'last_event_ts' => self::$lastEventTs,
            ];
        } catch (\Throwable $e) {
            return [
                'events_count'  => 0,
                'last_event'    => '',
                'last_event_ts' => 0,
            ];
        }
    }
}
