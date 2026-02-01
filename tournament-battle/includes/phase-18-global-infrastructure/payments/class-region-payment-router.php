<?php
declare(strict_types=1);

namespace TB\Phase18\Payments;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 13 / 25 — Region Payment Router (Read-Only Resolver)
 */
final class RegionPaymentRouter
{
    /**
     * Resolve payment routing metadata for a given region
     *
     * @param string $regionId
     * @param array<string,mixed> $currencyMatrix
     * @return array<string,mixed>
     */
    public static function resolve(string $regionId, array $currencyMatrix): array
    {
        if ($regionId === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: region_id must be non-empty string'
            );
        }

        if (
            !isset(
                $currencyMatrix['matrix'],
                $currencyMatrix['base_currency']
            )
            || !\is_array($currencyMatrix['matrix'])
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid currency matrix structure'
            );
        }

        if (!isset($currencyMatrix['matrix'][$regionId])) {
            throw new \RuntimeException(
                "Phase 18 hard fail: no currency mapping for region_id: {$regionId}"
            );
        }

        $regionCurrency = $currencyMatrix['matrix'][$regionId];

        if (
            !isset(
                $regionCurrency['currency_code'],
                $regionCurrency['rate'],
                $regionCurrency['is_base']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid region currency mapping structure'
            );
        }

        return [
            'region_id'      => $regionId,
            'currency_code'  => $regionCurrency['currency_code'],
            'rate'           => $regionCurrency['rate'],
            'is_base'        => $regionCurrency['is_base'],
            'base_currency'  => $currencyMatrix['base_currency'],
        ];
    }
}
