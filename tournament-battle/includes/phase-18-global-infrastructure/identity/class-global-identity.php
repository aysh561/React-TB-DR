<?php
declare(strict_types=1);

namespace TB\Phase18\Identity;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 14 / 25 — Global Identity Resolver (Read-Only)
 */
final class GlobalIdentity
{
    /**
     * Resolve and normalize identity payload into canonical structure
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function resolve(array $payload): array
    {
        if (
            !isset(
                $payload['identity_id'],
                $payload['source']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid identity payload structure'
            );
        }

        if (!\is_string($payload['identity_id']) || $payload['identity_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: identity_id must be non-empty string'
            );
        }

        if (!\is_string($payload['source']) || $payload['source'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: source must be non-empty string'
            );
        }

        return [
            'identity_id' => $payload['identity_id'],
            'source'      => $payload['source'],
        ];
    }
}
