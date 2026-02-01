<?php
/**
 * Phase 15 — Real-Time Engine
 * File 13/23 — Real-Time Verification / Referee Decision Push
 * Path: /includes/phase-15-realtime/push/class-rt-verification-push.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Push;

use Phase15\Push\RT_Push_Dispatcher;

final class RT_Verification_Push
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Push verification / referee messages to target connections.
     */
    public static function push(array $message, array $connectionIds): void
    {
        if (empty($message) || empty($connectionIds)) {
            return;
        }

        try {
            RT_Push_Dispatcher::dispatch($message, $connectionIds);
        } catch (\Throwable $e) {
            return;
        }
    }
}
