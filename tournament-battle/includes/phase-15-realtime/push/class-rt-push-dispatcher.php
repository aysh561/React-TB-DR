<?php
/**
 * Phase 15 — Real-Time Engine
 * File 7/22 — Real-Time Push Dispatcher
 * Path: /includes/phase-15-realtime/push/class-rt-push-dispatcher.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Push;

use Phase15\Transport\RT_WebSocket_Server;

final class RT_Push_Dispatcher
{
    /**
     * Loader compatibility only.
     * Koi execution ya state nahi.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Dispatch normalized message to given connection IDs.
     */
    public static function dispatch(array $message, array $connectionIds): void
    {
        if (empty($message) || empty($connectionIds)) {
            return;
        }

        try {
            $uniqueConnections = [];

            foreach ($connectionIds as $connectionId) {
                if (!is_string($connectionId) || $connectionId === '') {
                    continue;
                }

                if (isset($uniqueConnections[$connectionId])) {
                    continue;
                }

                $uniqueConnections[$connectionId] = true;
            }

            if (empty($uniqueConnections)) {
                return;
            }

            RT_WebSocket_Server::push(
                $message,
                array_keys($uniqueConnections)
            );

        } catch (\Throwable $e) {
            return;
        }
    }
}
