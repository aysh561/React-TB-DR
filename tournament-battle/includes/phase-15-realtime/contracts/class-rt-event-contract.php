<?php
/**
 * Phase 15 — Real-Time Engine
 * File 24/24 — Real-Time Event Contract
 * Path: /includes/phase-15-realtime/contracts/class-rt-event-contract.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Contracts;

final class RT_Event_Contract
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Build canonical real-time event envelope.
     */
    public static function build(
        string $event,
        int $version,
        array $payload,
        string $correlationId = ''
    ): array {
        try {
            if ($event === '') {
                $event = 'unknown.event';
            }

            if ($version <= 0) {
                $version = 1;
            }

            if (!is_array($payload)) {
                $payload = [];
            }

            return [
                'event' => [
                    'name'           => $event,
                    'version'        => $version,
                    'payload'        => $payload,
                    'correlation_id' => $correlationId,
                    'timestamp'      => time(),
                ],
            ];

        } catch (\Throwable $e) {
            return [
                'event' => [
                    'name'           => 'unknown.event',
                    'version'        => 1,
                    'payload'        => [],
                    'correlation_id' => '',
                    'timestamp'      => time(),
                ],
            ];
        }
    }
}
