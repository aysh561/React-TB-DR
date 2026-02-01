<?php
declare(strict_types=1);

namespace TB\Phase18\Regions;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 5 / 25 — Region Router (STRICT)
 */
final class RegionRouter
{
    /**
     * Resolve region by region_id
     *
     * @param string $regionId
     * @return array<string,mixed>
     */
    public static function resolveById(string $regionId): array
    {
        if (!RegionRegistry::exists($regionId)) {
            throw new \RuntimeException(
                "Phase 18 hard fail: unknown region_id: {$regionId}"
            );
        }

        return RegionRegistry::getById($regionId);
    }

    /**
     * Resolve region by region_code
     *
     * @param string $regionCode
     * @return array<string,mixed>
     */
    public static function resolveByCode(string $regionCode): array
    {
        return RegionRegistry::getByCode($regionCode);
    }
}
