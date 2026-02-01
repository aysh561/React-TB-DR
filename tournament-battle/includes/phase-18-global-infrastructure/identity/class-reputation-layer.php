<?php
declare(strict_types=1);

namespace TB\Phase18\Identity;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 15 / 25 — Global Reputation Layer (Read-Only)
 */
final class ReputationLayer
{
    /**
     * Resolve and normalize reputation payload into canonical structure
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function resolve(array $payload): array
    {
        if (
            !isset(
                $payload['identity_id'],
                $payload['score'],
                $payload['tier']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid reputation payload structure'
            );
        }

        if (!\is_string($payload['identity_id']) || $payload['identity_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: identity_id must be non-empty string'
            );
        }

        if (!\is_int($payload['score'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: score must be int'
            );
        }

        if (!\is_string($payload['tier']) || $payload['tier'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: tier must be non-empty string'
            );
        }

        return [
            'identity_id' => $payload['identity_id'],
            'score'       => $payload['score'],
            'tier'        => $payload['tier'],
        ];
    }
}
