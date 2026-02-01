<?php
/**
 * Phase 15 — Real-Time Engine
 * File 22/24 — Real-Time Error Contract
 * Path: /includes/phase-15-realtime/contracts/class-rt-error-contract.php
 */

if (!defined('ABSPATH')) {
    exit;
}

namespace Phase15\Contracts;

final class RT_Error_Contract
{
    /**
     * Loader compatibility only.
     */
    public static function register(): void
    {
        return;
    }

    /**
     * Build standardized real-time error payload.
     */
    public static function build(string $code, string $message, array $meta = []): array
    {
        try {
            if ($code === '') {
                $code = 'unknown_error';
            }

            if ($message === '') {
                $message = 'An unknown error occurred';
            }

            return [
                'error' => [
                    'code'      => $code,
                    'message'   => $message,
                    'meta'      => is_array($meta) ? $meta : [],
                    'timestamp' => time(),
                ],
            ];

        } catch (\Throwable $e) {
            return [
                'error' => [
                    'code'      => 'error_contract_failure',
                    'message'   => 'Failed to build error payload',
                    'meta'      => [],
                    'timestamp' => time(),
                ],
            ];
        }
    }
}
