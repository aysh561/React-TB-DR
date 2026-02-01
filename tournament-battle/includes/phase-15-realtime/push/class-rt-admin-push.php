<?php
/**
 * Phase 15 — Real-Time Engine
 * File 14/23 — Real-Time Admin Push
 * Path: /includes/phase-15-realtime/push/class-rt-admin-push.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Push;

use Phase15\Push\RT_Push_Dispatcher;

final class RT_Admin_Push
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Push admin-only real-time messages.
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
