<?php
declare(strict_types=1);

namespace TB\Phase18\Tournaments;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 11 / 25 — Timezone Scheduler (Read-Only Normalizer)
 */
final class TimezoneScheduler
{
    /**
     * Normalize region-level schedules to UTC reference
     *
     * @param array<int,array<string,mixed>> $regionSchedules
     * @return array<string,mixed>
     */
    public static function normalize(array $regionSchedules): array
    {
        if ($regionSchedules === []) {
            throw new \RuntimeException(
                'Phase 18 hard fail: empty region schedules payload'
            );
        }

        $normalized = [];
        $regions = [];

        foreach ($regionSchedules as $payload) {
            if (
                !isset(
                    $payload['region_id'],
                    $payload['timezone'],
                    $payload['timestamp']
                )
            ) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: invalid schedule payload structure'
                );
            }

            if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: region_id must be non-empty string'
                );
            }

            if (!\is_string($payload['timezone']) || $payload['timezone'] === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: timezone must be non-empty string'
                );
            }

            if (!\is_int($payload['timestamp'])) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: timestamp must be int'
                );
            }

            $regions[] = $payload['region_id'];

            $normalized[] = [
                'region_id'     => $payload['region_id'],
                'timestamp_utc' => $payload['timestamp'],
                'timezone'      => 'UTC',
            ];
        }

        return [
            'regions_count' => \count(\array_unique($regions)),
            'regions'       => \array_values(\array_unique($regions)),
            'schedules'     => $normalized,
        ];
    }
}
