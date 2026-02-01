<?php
declare(strict_types=1);

namespace TB\Phase18\Regions;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 4 / 25 — Region Registry (STRICT)
 */
final class RegionRegistry
{
    /**
     * Canonical read-only region registry
     *
     * @var array<string,array<string,mixed>>
     */
    private static array $regions = [
        'PK' => [
            'region_id'   => 'PK',
            'region_code' => 'PK',
            'region_name' => 'Pakistan',
            'status'      => 'active',
            'is_available'=> true,
        ],
        'IN' => [
            'region_id'   => 'IN',
            'region_code' => 'IN',
            'region_name' => 'India',
            'status'      => 'active',
            'is_available'=> true,
        ],
    ];

    /**
     * Get all registered regions
     *
     * @return array<string,array<string,mixed>>
     */
    public static function getAll(): array
    {
        return self::$regions;
    }

    /**
     * Get region by region_id
     *
     * @param string $regionId
     * @return array<string,mixed>
     */
    public static function getById(string $regionId): array
    {
        if (!isset(self::$regions[$regionId])) {
            throw new \RuntimeException(
                "Phase 18 hard fail: region not found by id: {$regionId}"
            );
        }

        return self::$regions[$regionId];
    }

    /**
     * Get region by region_code
     *
     * @param string $regionCode
     * @return array<string,mixed>
     */
    public static function getByCode(string $regionCode): array
    {
        foreach (self::$regions as $region) {
            if ($region['region_code'] === $regionCode) {
                return $region;
            }
        }

        throw new \RuntimeException(
            "Phase 18 hard fail: region not found by code: {$regionCode}"
        );
    }

    /**
     * Check if region exists
     *
     * @param string $regionId
     * @return bool
     */
    public static function exists(string $regionId): bool
    {
        return isset(self::$regions[$regionId]);
    }
}
