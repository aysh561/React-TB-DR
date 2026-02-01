<?php
/**
 * Phase 16 — Anti-Cheat Evidence Contract
 * File: /includes/phase-16-anti-cheat/contracts/class-ac-evidence-contract.php
 *
 * ROLE (STRICT):
 * - Evidence payload ka canonical, authoritative structure define karna
 * - Sirf shape + type enforcement
 * - Koi logic, scoring, inference, ya side-effects nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Evidence_Contract
{
    /**
     * Assert evidence payload structure
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    public static function assert(array $evidence): void
    {
        self::assertType($evidence, 'array', 'evidence');

        self::assertOptionalArray($evidence, 'file');
        self::assertOptionalArray($evidence, 'metadata');
        self::assertOptionalArray($evidence, 'context');
        self::assertOptionalArray($evidence, 'signals');
        self::assertOptionalArray($evidence, 'fusion');
        self::assertOptionalArray($evidence, 'decision');
        self::assertOptionalArray($evidence, 'governance');
        self::assertOptionalArray($evidence, 'appeal');
    }

    /**
     * Validate wrapper (no mutation)
     *
     * @param array $evidence
     * @return array
     * @throws RuntimeException
     */
    public static function validate(array $evidence): array
    {
        self::assert($evidence);
        return $evidence;
    }

    /**
     * Assert key exists (if present) and is array
     */
    private static function assertOptionalArray(array $data, string $key): void
    {
        if (!array_key_exists($key, $data)) {
            return;
        }

        if (!is_array($data[$key])) {
            throw new RuntimeException(
                sprintf('Evidence contract violation: "%s" must be an array', $key)
            );
        }
    }

    /**
     * Basic type assertion helper
     */
    private static function assertType($value, string $expected, string $label): void
    {
        $actual = gettype($value);

        if ($actual !== $expected) {
            throw new RuntimeException(
                sprintf(
                    'Evidence contract violation: "%s" expected %s, got %s',
                    $label,
                    $expected,
                    $actual
                )
            );
        }
    }
}
