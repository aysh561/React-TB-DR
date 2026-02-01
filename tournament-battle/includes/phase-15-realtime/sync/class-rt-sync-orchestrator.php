<?php
/**
 * Phase 15 — Real-Time Engine
 * File 9/23 — Real-Time Sync Orchestrator
 * Path: /includes/phase-15-realtime/sync/class-rt-sync-orchestrator.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Sync;

use Phase15\Contracts\EventEnvelope;
use Phase15\Transport\RT_Message_Protocol;
use Phase15\Transport\RT_Connection_Manager;
use Phase15\Push\RT_Push_Dispatcher;

final class RT_Sync_Orchestrator
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Orchestrate sync → push pipeline.
     */
    public static function handle(EventEnvelope $event, array $channels): void
    {
        if (empty($channels)) {
            return;
        }

        try {
            $connectionIds = [];

            foreach ($channels as $channel) {
                if (!is_string($channel) || $channel === '') {
                    continue;
                }

                $ids = RT_Connection_Manager::getByChannel($channel);
                if (!empty($ids)) {
                    $connectionIds = array_merge($connectionIds, $ids);
                }
            }

            if (empty($connectionIds)) {
                return;
            }

            $connectionIds = array_values(array_unique($connectionIds));

            $message = RT_Message_Protocol::build(
                $event,
                null,
                $channels
            );

            RT_Push_Dispatcher::dispatch($message, $connectionIds);

        } catch (\Throwable $e) {
            return;
        }
    }
}
