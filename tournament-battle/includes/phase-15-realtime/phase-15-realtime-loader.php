<?php
/**
 * Phase 15 — Real-Time Engine Loader
 * Path: /includes/phase-15-realtime/phase-15-realtime-loader.php
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Phase15_RealTime_Loader
{
    private static bool $booted = false;

    public static function boot(): void
    {
        // Phase 15A readiness contract (authoritative)
        if (
            !defined('PHASE_15A_ACTIVE') ||
            PHASE_15A_ACTIVE !== true ||
            !did_action('tb_phase_15a_ready')
        ) {
            return;
        }

        // Single boot guarantee
        if (self::$booted === true) {
            return;
        }

        // Fail-closed dependency checks (locked order)
        $requiredClasses = [
            // Core Bus
            'Phase15\\Core\\RealTimeEventBus',
            'Phase15\\Core\\ChannelRegistry',

            // Transport
            'Phase15\\Transport\\WebSocketServer',
            'Phase15\\Transport\\ConnectionManager',
            'Phase15\\Transport\\MessageProtocol',

            // Sync Engines
            'Phase15\\Sync\\MatchSync',
            'Phase15\\Sync\\PaymentSync',
            'Phase15\\Sync\\BracketSync',
            'Phase15\\Sync\\TournamentSync',

            // Push Engines
            'Phase15\\Push\\VerificationPush',
            'Phase15\\Push\\AdminPush',

            // Spectator Layer
            'Phase15\\Spectator\\SpectatorGateway',
            'Phase15\\Spectator\\SpectatorFilter',

            // Security
            'Phase15\\Security\\AuthGuard',
            'Phase15\\Security\\RateLimiter',
            'Phase15\\Security\\IntegrityCheck',

            // Diagnostics
            'Phase15\\Diagnostics\\HealthMonitor',
            'Phase15\\Diagnostics\\ObservabilityHooks',
        ];

        foreach ($requiredClasses as $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        // 1. Core Bus
        Phase15\Core\RealTimeEventBus::register();
        Phase15\Core\ChannelRegistry::register();

        // 2. Transport (registration only)
        Phase15\Transport\WebSocketServer::register();
        Phase15\Transport\ConnectionManager::register();
        Phase15\Transport\MessageProtocol::register();

        // 3. Sync Engines
        Phase15\Sync\MatchSync::register();
        Phase15\Sync\PaymentSync::register();
        Phase15\Sync\BracketSync::register();
        Phase15\Sync\TournamentSync::register();

        // 4. Push Engines
        Phase15\Push\VerificationPush::register();
        Phase15\Push\AdminPush::register();

        // 5. Spectator Layer
        Phase15\Spectator\SpectatorGateway::register();
        Phase15\Spectator\SpectatorFilter::register();

        // 6. Security
        Phase15\Security\AuthGuard::register();
        Phase15\Security\RateLimiter::register();
        Phase15\Security\IntegrityCheck::register();

        // 7. Diagnostics
        Phase15\Diagnostics\HealthMonitor::register();
        Phase15\Diagnostics\ObservabilityHooks::register();

        self::$booted = true;
    }
}
if (!defined('TB_PHASE_15_READY')) {
    define('TB_PHASE_15_READY', true);
}
