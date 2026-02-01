<?php
declare(strict_types=1);

namespace TB\Phase18\Tournaments;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 10 / 25 — Cross-Region Brackets (Read-Only Aggregator)
 */
final class CrossRegionBrackets
{
    /**
     * Build global read-only brackets snapshot from region-level brackets
     *
     * @param array<int,array<string,mixed>> $regionBrackets
     * @return array<string,mixed>
     */
    public static function buildSnapshot(array $regionBrackets): array
    {
        if ($regionBrackets === []) {
            throw new \RuntimeException(
                'Phase 18 hard fail: empty region brackets payload'
            );
        }

        $groupedByRegion = [];
        $regions = [];
        $lastUpdated = null;

        foreach ($regionBrackets as $payload) {
            if (
                !isset(
                    $payload['region_id'],
                    $payload['brackets'],
                    $payload['updated_at']
                )
            ) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: invalid bracket payload structure'
                );
            }

            if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: region_id must be non-empty string'
                );
            }

            if (!\is_array($payload['brackets'])) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: brackets must be array'
                );
            }

            if (!\is_int($payload['updated_at'])) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: updated_at must be int'
                );
            }

            $regionId = $payload['region_id'];

            $groupedByRegion[$regionId] = $payload['brackets'];
            $regions[] = $regionId;

            if ($lastUpdated === null || $payload['updated_at'] > $lastUpdated) {
                $lastUpdated = $payload['updated_at'];
            }
        }

        return [
            'regions_count' => \count(\array_unique($regions)),
            'regions'       => \array_values(\array_unique($regions)),
            'brackets'      => $groupedByRegion,
            'updated_at'    => $lastUpdated,
        ];
    }
}
