<?php
declare(strict_types=1);

namespace TB\Phase18\UI;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 18 / 25 — Global UI Models (Read-Only View Normalizer)
 */
final class GlobalModels
{
    /**
     * Map normalized domain payload into UI-friendly global model
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function map(array $payload): array
    {
        if (
            !isset(
                $payload['tournament_id'],
                $payload['region_id'],
                $payload['state'],
                $payload['updated_at']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid UI payload structure'
            );
        }

        if (!\is_string($payload['tournament_id']) || $payload['tournament_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: tournament_id must be non-empty string'
            );
        }

        if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: region_id must be non-empty string'
            );
        }

        if (!\is_string($payload['state']) || $payload['state'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: state must be non-empty string'
            );
        }

        if (!\is_int($payload['updated_at'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: updated_at must be int'
            );
        }

        return [
            'tournamentId' => $payload['tournament_id'],
            'regionId'     => $payload['region_id'],
            'state'        => $payload['state'],
            'updatedAt'    => $payload['updated_at'],
        ];
    }
}
