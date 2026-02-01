<?php
/**
 * Phase 15 — Real-Time Engine
 * File 20/24 — Real-Time Health Monitor
 * Path: /includes/phase-15-realtime/diagnostics/class-rt-health-monitor.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Diagnostics;

final class RT_Health_Monitor
{
    /**
     * Process-local metrics
     */
    private static int $activeConnections  = 0;
    private static int $totalConnections   = 0;
    private static int $droppedConnections = 0;

    private static int $heartbeatCount     = 0;
    private static int $totalLatencyMs     = 0;
    private static int $lastHeartbeatTs    = 0;

    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Record new connection opened.
     */
    public static function connectionOpened(): void
    {
        try {
            self::$activeConnections++;
            self::$totalConnections++;
        } catch (\Throwable $e) {
            return;
        }
    }

    /**
     * Record connection closed / dropped.
     */
    public static function connectionClosed(): void
    {
        try {
            if (self::$activeConnections > 0) {
                self::$activeConnections--;
            }
            self::$droppedConnections++;
        } catch (\Throwable $e) {
            return;
        }
    }

    /**
     * Record heartbeat / latency signal.
     */
    public static function heartbeat(int $latencyMs): void
    {
        try {
            if ($latencyMs < 0) {
                return;
            }

            self::$heartbeatCount++;
            self::$totalLatencyMs += $latencyMs;
            self::$lastHeartbeatTs = time();
        } catch (\Throwable $e) {
            return;
        }
    }

    /**
     * Snapshot of current health metrics.
     *
     * @return array<string, int>
     */
    public static function snapshot(): array
    {
        try {
            $avgLatency = 0;

            if (self::$heartbeatCount > 0) {
                $avgLatency = (int) floor(
                    self::$totalLatencyMs / self::$heartbeatCount
                );
            }

            return [
                'active_connections'   => self::$activeConnections,
                'total_connections'    => self::$totalConnections,
                'dropped_connections'  => self::$droppedConnections,
                'avg_latency_ms'       => $avgLatency,
                'last_heartbeat'       => self::$lastHeartbeatTs,
            ];

        } catch (\Throwable $e) {
            return [
                'active_connections'   => 0,
                'total_connections'    => 0,
                'dropped_connections'  => 0,
                'avg_latency_ms'       => 0,
                'last_heartbeat'       => 0,
            ];
        }
    }
}
