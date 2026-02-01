<?php
/**
 * Phase 15 — Real-Time Engine
 * File 15/23 — Real-Time Spectator Read-Only Gateway
 * Path: /includes/phase-15-realtime/spectator/class-rt-spectator-gateway.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Spectator;

use Phase15\Spectator\RT_Spectator_Filter;
use Phase15\Push\RT_Push_Dispatcher;

final class RT_Spectator_Gateway
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Push spectator-safe real-time messages.
     */
    public static function push(array $message, array $connectionIds): void
    {
        if (empty($message) || empty($connectionIds)) {
            return;
        }

        try {
            $filteredMessage = RT_Spectator_Filter::filter($message);

            if (empty($filteredMessage)) {
                return;
            }

            RT_Push_Dispatcher::dispatch($filteredMessage, $connectionIds);
        } catch (\Throwable $e) {
            return;
        }
    }
}
