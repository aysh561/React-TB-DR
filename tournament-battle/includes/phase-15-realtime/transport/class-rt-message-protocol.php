```php
<?php
/**
 * Phase 15 — Real-Time Engine
 * File 4/22 — Real-Time Message Protocol
 * Path: /includes/phase-15-realtime/transport/class-rt-message-protocol.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Transport;

use Phase15\Contracts\EventEnvelope;
use Phase15\Contracts\DispatchResult;

final class RT_Message_Protocol
{
    /**
     * Loader compatibility only.
     * Koi runtime state ya logic nahi.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Build canonical real-time message payload.
     *
     * @return array<string, mixed>
     */
    public static function build(
        EventEnvelope $event,
        DispatchResult $result,
        array $channels
    ): array {
        try {
            return [
                'type'           => $event->getEventName(),
                'version'        => $event->getVersion(),
                'correlation_id' => $event->getCorrelationId(),
                'channels'       => array_values($channels),
                'payload'        => $event->getPayloadSnapshot(),
                'meta'           => [
                    'timestamp'      => time(),
                    'status'         => $result->isSuccess() ? 'success' : 'failed',
                    'handled_count'  => $result->getHandledCount(),
                ],
            ];

        } catch (\Throwable $e) {
            return [
                'type'           => 'unknown',
                'version'        => 0,
                'correlation_id' => '',
                'channels'       => [],
                'payload'        => null,
                'meta'           => [
                    'timestamp'      => time(),
                    'status'         => 'failed',
                    'handled_count'  => 0,
                ],
            ];
        }
    }
}
```
