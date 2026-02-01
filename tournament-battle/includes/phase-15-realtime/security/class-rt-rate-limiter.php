<?php
/**
 * Phase 15 — Real-Time Engine
 * File 18/24 — Real-Time Rate Limiter (Production + Memory Safe)
 * Path: /includes/phase-15-realtime/security/class-rt-rate-limiter.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Security;

final class RT_Rate_Limiter
{
    /**
     * Process-local counters:
     * [connectionId][action] => ['count' => int, 'window' => int]
     *
     * @var array<string, array<string, array{count:int, window:int}>>
     */
    private static array $counters = [];

    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Rate-limit decision for a connection + action.
     */
    public static function allow(string $connectionId, string $action): bool
    {
        try {
            if ($connectionId === '' || $action === '') {
                return false;
            }

            $now           = time();
            $windowSeconds = 5;
            $maxRequests   = 10;

            /**
             * -------- GLOBAL CLEANUP (MEMORY SAFE) --------
             * - Expired action windows remove
             * - Empty connections purge
             */
            foreach (self::$counters as $connId => $actions) {
                foreach ($actions as $act => $data) {
                    if (($now - $data['window']) >= $windowSeconds) {
                        unset(self::$counters[$connId][$act]);
                    }
                }

                if (empty(self::$counters[$connId])) {
                    unset(self::$counters[$connId]);
                }
            }
            /** ------------------------------------------- */

            if (!isset(self::$counters[$connectionId][$action])) {
                self::$counters[$connectionId][$action] = [
                    'count'  => 0,
                    'window' => $now,
                ];
            }

            $entry = &self::$counters[$connectionId][$action];

            // Window rollover
            if (($now - $entry['window']) >= $windowSeconds) {
                $entry['count']  = 0;
                $entry['window'] = $now;
            }

            $entry['count']++;

            if ($entry['count'] > $maxRequests) {
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            return false;
        }
    }
}
