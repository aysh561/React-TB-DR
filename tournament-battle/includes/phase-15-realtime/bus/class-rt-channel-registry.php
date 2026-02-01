<?php
/**
 * Phase 15 — Real-Time Engine
 * File 3/22 — Real-Time Channel Registry
 * Path: /includes/phase-15-realtime/bus/class-rt-channel-registry.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Bus;

use Phase15\Contracts\EventEnvelope;

final class RT_Channel_Registry
{
    /**
     * Loader compatibility only.
     * Koi runtime state ya mutation nahi.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Resolve event → channels mapping.
     * Pure, deterministic, stateless.
     *
     * @return string[]
     */
    public static function resolve(EventEnvelope $event): array
    {
        try {
            $eventName = $event->getEventName();
            $version   = $event->getVersion();

            switch ($eventName) {

                case 'match.updated':
                    if ($version === 1) {
                        $matchId = $event->getPayloadValue('match_id');
                        if ($matchId !== null) {
                            return [
                                'match:' . (string) $matchId,
                            ];
                        }
                    }
                    break;

                case 'match.state.changed':
                    if ($version === 1) {
                        $matchId = $event->getPayloadValue('match_id');
                        if ($matchId !== null) {
                            return [
                                'match:' . (string) $matchId,
                            ];
                        }
                    }
                    break;

                case 'payment.state.changed':
                    if ($version === 1) {
                        $tournamentId = $event->getPayloadValue('tournament_id');
                        if ($tournamentId !== null) {
                            return [
                                'tournament:' . (string) $tournamentId,
                            ];
                        }
                    }
                    break;

                case 'tournament.updated':
                    if ($version === 1) {
                        $tournamentId = $event->getPayloadValue('tournament_id');
                        if ($tournamentId !== null) {
                            return [
                                'tournament:' . (string) $tournamentId,
                            ];
                        }
                    }
                    break;

                case 'admin.alert':
                    if ($version === 1) {
                        return [
                            'admin:global',
                        ];
                    }
                    break;
            }

            return [];

        } catch (\Throwable $e) {
            return [];
        }
    }
}
