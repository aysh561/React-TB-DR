<?php
/**
 * Phase 15 — Real-Time Engine
 * File 23/24 — Real-Time State Snapshot Contract
 * Path: /includes/phase-15-realtime/contracts/class-rt-state-contract.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Contracts;

final class RT_State_Contract
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Build standardized real-time state snapshot.
     */
    public static function build(string $type, int $version, array $state, array $meta = []): array
    {
        try {
            if ($type === '') {
                $type = 'unknown';
            }

            if ($version <= 0) {
                $version = 1;
            }

            if (!is_array($state)) {
                $state = [];
            }

            return [
                'state' => [
                    'type'      => $type,
                    'version'   => $version,
                    'data'      => $state,
                    'meta'      => is_array($meta) ? $meta : [],
                    'timestamp' => time(),
                ],
            ];

        } catch (\Throwable $e) {
            return [
                'state' => [
                    'type'      => 'unknown',
                    'version'   => 1,
                    'data'      => [],
                    'meta'      => [],
                    'timestamp' => time(),
                ],
            ];
        }
    }
}
