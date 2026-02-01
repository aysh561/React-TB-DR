<?php
declare(strict_types=1);

namespace TB\Phase18\Deployment;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 22 / 25 — Global Governance Resolver (Read-Only Policy Snapshot)
 */
final class GlobalGovernance
{
    /**
     * Observe and normalize governance payload (read-only, stateless)
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function resolve(array $payload): array
    {
        if (
            !isset(
                $payload['policy_id'],
                $payload['scope'],
                $payload['status']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid governance payload structure'
            );
        }

        if (!\is_string($payload['policy_id']) || $payload['policy_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: policy_id must be non-empty string'
            );
        }

        if (!\is_string($payload['scope']) || $payload['scope'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: scope must be non-empty string'
            );
        }

        if (!\is_bool($payload['status'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: status must be boolean'
            );
        }

        return [
            'policy_id' => $payload['policy_id'],
            'scope'     => $payload['scope'],
            'status'    => $payload['status'],
        ];
    }
}
