<?php
declare(strict_types=1);

namespace TB\Phase18\Payments;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 12 / 25 — Multi-Currency Matrix (Read-Only Normalizer)
 */
final class MultiCurrencyMatrix
{
    /**
     * Normalize region-level currency configurations into a read-only matrix
     *
     * @param array<int,array<string,mixed>> $regionCurrencies
     * @return array<string,mixed>
     */
    public static function normalize(array $regionCurrencies): array
    {
        if ($regionCurrencies === []) {
            throw new \RuntimeException(
                'Phase 18 hard fail: empty region currency payload'
            );
        }

        $matrix = [];
        $supportedCurrencies = [];
        $regions = [];
        $baseCurrency = null;

        foreach ($regionCurrencies as $payload) {
            if (
                !isset(
                    $payload['region_id'],
                    $payload['currency_code'],
                    $payload['rate'],
                    $payload['is_base']
                )
            ) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: invalid currency payload structure'
                );
            }

            if (!\is_string($payload['region_id']) || $payload['region_id'] === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: region_id must be non-empty string'
                );
            }

            if (!\is_string($payload['currency_code']) || $payload['currency_code'] === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: currency_code must be non-empty string'
                );
            }

            if (!\is_numeric($payload['rate'])) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: rate must be numeric (opaque)'
                );
            }

            if (!\is_bool($payload['is_base'])) {
                throw new \RuntimeException(
                    'Phase 18 hard fail: is_base must be boolean'
                );
            }

            $regionId = $payload['region_id'];
            $currency = $payload['currency_code'];

            $regions[] = $regionId;
            $supportedCurrencies[] = $currency;

            if ($payload['is_base'] === true) {
                $baseCurrency = $currency;
            }

            $matrix[$regionId] = [
                'currency_code' => $currency,
                'rate'          => $payload['rate'],
                'is_base'       => $payload['is_base'],
            ];
        }

        return [
            'regions_count'       => \count(\array_unique($regions)),
            'regions'             => \array_values(\array_unique($regions)),
            'supported_currencies'=> \array_values(\array_unique($supportedCurrencies)),
            'base_currency'       => $baseCurrency,
            'matrix'              => $matrix,
        ];
    }
}
