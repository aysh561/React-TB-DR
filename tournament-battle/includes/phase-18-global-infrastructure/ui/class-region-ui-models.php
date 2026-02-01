<?php
declare(strict_types=1);

namespace TB\Phase18\UI;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 27 / 27 — Region-Specific UI Models (Read-Only Normalizer)
 */
final class RegionUIModels
{
    /**
     * Normalize region-specific UI payload into canonical structure
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function normalize(array $payload): array
    {
        if (
            !isset(
                $payload['region_id'],
                $payload['ui_payload']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid region UI payload structure'
            );
        }

        if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: region_id must be non-empty string'
            );
        }

        if (!\is_array($payload['ui_payload'])) {
            throw new \RuntimeException(
                'Phase 18 hard fail: ui_payload must be array'
            );
        }

        return [
            'region_id'  => $payload['region_id'],
            'ui_payload' => $payload['ui_payload'],
        ];
    }
}
