<?php
declare(strict_types=1);

namespace TB\Phase18\Tournaments;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 9 / 25 — Global Tournament Engine (Aggregator / Orchestrator)
 */
final class GlobalTournamentEngine
{
    /**
     * Aggregate region-level tournament payloads into a global snapshot
     *
     * @param array<int,array<string,mixed>> $regionPayloads
     * @return array<string,mixed>
     */
    public static function buildGlobalSnapshot(array $regionPayloads): array
    {
        if ($regionPayloads === []) {
            throw new \RuntimeException(
                'Phase 18 hard fail: empty region payloads'
            );
        }

        $normalized = [];
        $regions = [];
        $latestTimestamp = null;

        foreach ($regionPayloads as $payload) {
            if (
                !isset(
                    $payload['region_id'],
                    $payload['tournament_id'],
                    $payload['state'],
                    $payload['updated_at']
                )
            ) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: invalid tournament payload structure'
                );
            }

            if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: region_id must be non-empty string'
                );
            }

            if (!\is_int($payload['updated_at'])) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: updated_at must be int'
                );
            }

            $regions[] = $payload['region_id'];
            $normalized[] = $payload;

            if ($latestTimestamp === null || $payload['updated_at'] > $latestTimestamp) {
                $latestTimestamp = $payload['updated_at'];
            }
        }

        return [
            'regions_count' => \count(\array_unique($regions)),
            'regions'       => \array_values(\array_unique($regions)),
            'tournaments'   => $normalized,
            'generated_at'  => $latestTimestamp,
        ];
    }
}
