<?php
declare(strict_types=1);

namespace TB\Phase18\UI;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 26 / 27 — Multi-Language UI Normalizer (Read-Only)
 */
final class MultiLanguageUI
{
    /**
     * Normalize UI language payload into canonical structure
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public static function normalize(array $payload): array
    {
        if (
            !isset(
                $payload['language_code'],
                $payload['label']
            )
        ) {
            throw new \RuntimeException(
                'Phase 18 hard fail: invalid language payload structure'
            );
        }

        if (!\is_string($payload['language_code']) || $payload['language_code'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: language_code must be non-empty string'
            );
        }

        if (!\is_string($payload['label']) || $payload['label'] === '') {
            throw new \RuntimeException(
                'Phase 18 hard fail: label must be non-empty string'
            );
        }

        return [
            'language_code' => $payload['language_code'],
            'label'         => $payload['label'],
        ];
    }
}
