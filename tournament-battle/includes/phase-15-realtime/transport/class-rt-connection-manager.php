<?php
/**
 * Phase 15 — Real-Time Engine
 * File 5/22 — Real-Time Connection Manager
 * Path: /includes/phase-15-realtime/transport/class-rt-connection-manager.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Transport;

final class RT_Connection_Manager
{
    /**
     * Process-local connection → channels map
     * @var array<string, array<int, string>>
     */
    private static array $connections = [];

    /**
     * Channel → connectionIds index
     * @var array<string, array<int, string>>
     */
    private static array $channels = [];

    /**
     * Connection metadata (read-only exposure)
     * @var array<string, array>
     */
    private static array $meta = [];

    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Attach a connection to channels.
     */
    public static function attach(string $connectionId, array $channels, array $connectionMeta = []): void
    {
        if ($connectionId === '') {
            return;
        }

        self::detach($connectionId);

        self::$connections[$connectionId] = [];
        self::$meta[$connectionId] = $connectionMeta;

        foreach ($channels as $channel) {
            if (!is_string($channel) || $channel === '') {
                continue;
            }

            if (!in_array($channel, self::$connections[$connectionId], true)) {
                self::$connections[$connectionId][] = $channel;
            }

            if (!isset(self::$channels[$channel])) {
                self::$channels[$channel] = [];
            }

            if (!in_array($connectionId, self::$channels[$channel], true)) {
                self::$channels[$channel][] = $connectionId;
            }
        }
    }

    /**
     * Detach and clean up a connection.
     */
    public static function detach(string $connectionId): void
    {
        if (!isset(self::$connections[$connectionId])) {
            return;
        }

        foreach (self::$connections[$connectionId] as $channel) {
            if (isset(self::$channels[$channel])) {
                self::$channels[$channel] = array_values(
                    array_filter(
                        self::$channels[$channel],
                        static function ($id) use ($connectionId) {
                            return $id !== $connectionId;
                        }
                    )
                );

                if (empty(self::$channels[$channel])) {
                    unset(self::$channels[$channel]);
                }
            }
        }

        unset(self::$connections[$connectionId], self::$meta[$connectionId]);
    }

    /**
     * Get all connection IDs subscribed to a channel.
     *
     * @return string[]
     */
    public static function getByChannel(string $channel): array
    {
        if (!isset(self::$channels[$channel])) {
            return [];
        }

        return array_values(self::$channels[$channel]);
    }

    /**
     * Get read-only metadata for a connection.
     */
    public static function getMeta(string $connectionId): array
    {
        if (!isset(self::$meta[$connectionId])) {
            return [];
        }

        return self::$meta[$connectionId];
    }
}
