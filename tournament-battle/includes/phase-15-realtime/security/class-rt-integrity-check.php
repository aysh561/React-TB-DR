<?php
/**
 * Phase 15 — Real-Time Engine
 * File 19/24 — Real-Time Integrity / Anti-Tamper Signal Guard
 * Path: /includes/phase-15-realtime/security/class-rt-integrity-check.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Security;

final class RT_Integrity_Check
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Validate integrity of incoming real-time payload.
     */
    public static function validate(array $payload): bool
    {
        try {
            // Empty or malformed payload → deny
            if (empty($payload) || !is_array($payload)) {
                return false;
            }

            // Required structural keys
            $requiredKeys = ['type', 'version', 'payload'];
            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $payload)) {
                    return false;
                }
            }

            // Basic type checks
            if (!is_string($payload['type']) || $payload['type'] === '') {
                return false;
            }

            if (!is_int($payload['version']) || $payload['version'] <= 0) {
                return false;
            }

            if (!is_array($payload['payload'])) {
                return false;
            }

            // Blocked control / tamper keys (hard deny)
            $blockedKeys = [
                'execute',
                'command',
                'write',
                'update',
                'delete',
                'admin',
                'referee',
                'auth',
                'token',
            ];

            // Check blocked keys at top-level
            foreach ($blockedKeys as $blocked) {
                if (array_key_exists($blocked, $payload)) {
                    return false;
                }
            }

            // Check blocked keys inside payload (nested control intent)
            foreach ($blockedKeys as $blocked) {
                if (array_key_exists($blocked, $payload['payload'])) {
                    return false;
                }
            }

            // Primitive size sanity (anti-flood)
            if (count($payload['payload']) > 1000) {
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            return false;
        }
    }
}
