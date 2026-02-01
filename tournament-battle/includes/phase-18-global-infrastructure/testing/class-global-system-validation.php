<?php
declare(strict_types=1);

namespace TB\Phase18\Testing;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 23 / 25 — Global System Validation (Read-Only Integrity Check)
 */
final class GlobalSystemValidation
{
    /**
     * Validate structural integrity of multiple normalized payloads
     *
     * @param array<string,mixed> $payloads
     * @return array<string,mixed>
     */
    public static function validate(array $payloads): array
    {
        if ($payloads === []) {
            throw new \RuntimeException(
                'Phase 18 hard fail: empty system validation payload'
            );
        }

        $validatedSections = [];

        foreach ($payloads as $section => $payload) {
            if (!\is_string($section) || $section === '') {
                throw new \RuntimeException(
                    'Phase 18 hard fail: invalid section identifier'
                );
            }

            if (!\is_array($payload)) {
                throw new \RuntimeException(
                    "Phase 18 hard fail: payload for section '{$section}' must be array"
                );
            }

            if ($payload === []) {
                throw new \RuntimeException(
                    "Phase 18 hard fail: payload for section '{$section}' is empty"
                );
            }

            $validatedSections[] = $section;
        }

        return [
            'status'   => true,
            'sections' => $validatedSections,
        ];
    }
}
