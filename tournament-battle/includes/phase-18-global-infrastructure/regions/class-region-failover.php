<?php
declare(strict_types=1);

namespace TB\Phase18\Regions;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 6 / 25 — Region Failover (STRICT)
 */
final class RegionFailover
{
    /**
     * Evaluate if failover is required for given region_id
     *
     * @param string $regionId
     * @return bool
     */
    public static function isFailoverRequired(string $regionId): bool
    {
        $region = RegionRouter::resolveById($regionId);

        return ($region['is_available'] !== true);
    }

    /**
     * Suggest alternative region (resolution only)
     *
     * @param string $regionId
     * @return array<string,mixed>
     */
    public static function suggestAlternative(string $regionId): array
    {
        $primary = RegionRouter::resolveById($regionId);

        if ($primary['is_available'] === true) {
            throw new \RuntimeException(
                "Phase 18 hard fail: failover not required for region_id: {$regionId}"
            );
        }

        $regions = RegionRegistry::getAll();

        foreach ($regions as $id => $region) {
            if ($id === $regionId) {
                continue;
            }

            if (($region['is_available'] ?? false) === true) {
                return $region;
            }
        }

        throw new \RuntimeException(
            "Phase 18 hard fail: no available failover region for region_id: {$regionId}"
        );
    }
}
