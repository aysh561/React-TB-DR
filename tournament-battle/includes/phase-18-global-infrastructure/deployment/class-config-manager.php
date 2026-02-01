<?php
declare(strict_types=1);

namespace TB\Phase18\Deployment;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 21 / 25 — Global Deployment Config Resolver (Read-Only)
 */
final class ConfigManager
{
    /**
     * Observe and normalize deployment config payload (read-only, stateless)
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function resolve(array $payload): array
    {
        if (
            !isset(
                $payload['env'],
                $payload['version'],
                $payload['mode']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid deployment config payload structure'
            );
        }

        if (!\is_string($payload['env']) || $payload['env'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: env must be non-empty string'
            );
        }

        if (!\is_string($payload['version']) || $payload['version'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: version must be non-empty string'
            );
        }

        if (!\is_bool($payload['mode'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: mode must be boolean'
            );
        }

        return [
            'env'     => $payload['env'],
            'version' => $payload['version'],
            'mode'    => $payload['mode'],
        ];
    }
}
