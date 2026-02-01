<?php
/**
 * Phase 15 — Real-Time Engine
 * File 17/24 — Real-Time Authentication & Permission Guard
 * Path: /includes/phase-15-realtime/security/class-rt-auth-guard.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Security;

final class RT_Auth_Guard
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Validate connection metadata against access context.
     *
     * @param array  $connectionMeta
     * @param string $context (spectator | user | admin)
     */
    public static function validate(array $connectionMeta, string $context): bool
    {
        try {
            if ($context === '') {
                return false;
            }

            switch ($context) {

                case 'spectator':
                    // Spectator ke liye auth/meta optional hai, read-only implied
                    return true;

                case 'user':
                    // Valid user identifier required
                    if (
                        isset($connectionMeta['user_id']) &&
                        is_scalar($connectionMeta['user_id']) &&
                        (string) $connectionMeta['user_id'] !== ''
                    ) {
                        return true;
                    }
                    return false;

                case 'admin':
                    // Admin ke liye explicit admin flag + stable identifier dono required
                    if (
                        isset($connectionMeta['is_admin']) &&
                        $connectionMeta['is_admin'] === true &&
                        isset($connectionMeta['user_id']) &&
                        is_scalar($connectionMeta['user_id']) &&
                        (string) $connectionMeta['user_id'] !== ''
                    ) {
                        return true;
                    }
                    return false;

                default:
                    return false;
            }

        } catch (\Throwable $e) {
            return false;
        }
    }
}
