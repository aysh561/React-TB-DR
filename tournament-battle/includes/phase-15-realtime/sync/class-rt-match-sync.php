```php
<?php
/**
 * Phase 15 — Real-Time Engine
 * File 8/23 — Real-Time Match State Sync
 * Path: /includes/phase-15-realtime/sync/class-rt-match-sync.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Sync;

use Phase15\Contracts\EventEnvelope;
use Phase15\Bus\RT_Channel_Registry;

final class RT_Match_Sync
{
    /**
     * Register match-related hooks.
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Handle match-related events only.
     * Sirf filtering + channel resolution.
     * Push / dispatch next layer me hoga.
     */
    public static function handle(EventEnvelope $event): void
    {
        try {
            $eventName = $event->getEventName();
            $version   = $event->getVersion();

            if (
                $version !== 1 ||
                !in_array($eventName, ['match.updated', 'match.state.changed'], true)
            ) {
                return;
            }

            $channels = RT_Channel_Registry::resolve($event);

            if (empty($channels)) {
                return;
            }

            /**
             * Next layer (File 9/23) yahan se consume karegi:
             * EventEnvelope + resolved channels
             */
            return;

        } catch (\Throwable $e) {
            return;
        }
    }
}
```
