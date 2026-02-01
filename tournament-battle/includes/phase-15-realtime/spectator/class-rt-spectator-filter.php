<?php
/**
 * Phase 15 — Real-Time Engine
 * File 16/24 — Real-Time Spectator Payload Filter (Security Hardened)
 * Path: /includes/phase-15-realtime/spectator/class-rt-spectator-filter.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Spectator;

final class RT_Spectator_Filter
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Spectator-safe payload filter (ALLOW-LIST ONLY).
     */
    public static function filter(array $message): array
    {
        try {
            if (empty($message) || !is_array($message)) {
                return [];
            }

            // Strict allow-list for top-level keys
            $allowedTopLevel = [
                'type',
                'version',
                'channels',
                'payload',
                'meta',
                'timestamp',
                'status',
                'handled_count',
            ];

            $filtered = [];

            foreach ($allowedTopLevel as $key) {
                if (array_key_exists($key, $message)) {
                    $filtered[$key] = $message[$key];
                }
            }

            // Payload allow-list (read-only public data only)
            if (isset($filtered['payload']) && is_array($filtered['payload'])) {
                $allowedPayloadKeys = [
                    'state',
                    'scores',
                    'timers',
                    'labels',
                    'names',
                    'flags',
                ];

                $safePayload = [];

                foreach ($allowedPayloadKeys as $pKey) {
                    if (array_key_exists($pKey, $filtered['payload'])) {
                        $safePayload[$pKey] = $filtered['payload'][$pKey];
                    }
                }

                $filtered['payload'] = $safePayload;
            }

            // Meta allow-list (strict)
            if (isset($filtered['meta']) && is_array($filtered['meta'])) {
                $allowedMetaKeys = [
                    'engine',
                    'timestamp',
                    'status',
                ];

                $safeMeta = [];

                foreach ($allowedMetaKeys as $mKey) {
                    if (array_key_exists($mKey, $filtered['meta'])) {
                        $safeMeta[$mKey] = $filtered['meta'][$mKey];
                    }
                }

                $filtered['meta'] = $safeMeta;
            }

            return $filtered;

        } catch (\Throwable $e) {
            return [];
        }
    }
}
