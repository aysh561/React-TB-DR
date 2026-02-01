<?php
/**
 * Phase 15 — Real-Time Engine
 * File 2/22 — Real-Time Event Bus
 * Path: /includes/phase-15-realtime/bus/class-rt-event-bus.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Bus;

use Phase15\Contracts\EventEnvelope;
use Phase15\Contracts\DispatchResult;
use Phase15\Core\EngineExecutionGateway;

final class RT_Event_Bus
{
    /**
     * Loader compatibility only.
     * Koi state, wiring, ya execution nahi.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Pure stateless dispatch forwarder.
     */
    public static function dispatch(EventEnvelope $event): DispatchResult
    {
        try {
            if (!class_exists(EngineExecutionGateway::class)) {
                return DispatchResult::failed(
                    $event->getEventName(),
                    $event->getVersion(),
                    $event->getCorrelationId(),
                    'execution_gateway_missing'
                );
            }

            return EngineExecutionGateway::handle($event);
        } catch (\Throwable $e) {
            return DispatchResult::failed(
                $event->getEventName(),
                $event->getVersion(),
                $event->getCorrelationId(),
                'dispatch_exception'
            );
        }
    }
}
