<?php
/**
 * Phase 15 — Real-Time Engine
 * File 12/23 — Real-Time Tournament State Sync
 * Path: /includes/phase-15-realtime/sync/class-rt-tournament-sync.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Sync;

use Phase15\Contracts\EventEnvelope;
use Phase15\Bus\RT_Channel_Registry;

final class RT_Tournament_Sync
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Handle tournament-related events only.
     * Sirf filtering + channel resolution.
     * Orchestration next layer (RT_Sync_Orchestrator) me hogi.
     */
    public static function handle(EventEnvelope $event): void
    {
        try {
            $eventName = $event->getEventName();
            $version   = $event->getVersion();

            if (
                $version !== 1 ||
                !in_array(
                    $eventName,
                    ['tournament.updated', 'tournament.state.changed'],
                    true
                )
            ) {
                return;
            }

            $channels = RT_Channel_Registry::resolve($event);

            if (empty($channels)) {
                return;
            }

            /**
             * Next layer (File 9/23) yahan se consume karegi:
             * EventEnvelope + resolved channels
             */
            return;

        } catch (\Throwable $e) {
            return;
        }
    }
}
