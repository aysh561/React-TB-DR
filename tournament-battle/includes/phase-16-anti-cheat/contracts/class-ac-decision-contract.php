<?php
/**
 * Phase 16 — Anti-Cheat Decision Contract
 * File: /includes/phase-16-anti-cheat/contracts/class-ac-decision-contract.php
 *
 * ROLE (STRICT):
 * - Decision object ka canonical, authoritative structure define karna
 * - Sirf shape + type enforcement
 * - Koi logic, threshold, ya execution nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Decision_Contract
{
    /**
     * Assert decision structure
     *
     * @param array $decision
     * @throws RuntimeException
     */
    public static function assert(array $decision): void
    {
        self::assertType($decision, 'array', 'decision');

        self::assertRequiredString($decision, 'state');
        self::assertRequiredFloat($decision, 'score');
        self::assertRequiredString($decision, 'reason');
        self::assertRequiredArray($decision, 'breakdown');
    }

    /**
     * Validate wrapper (no mutation)
     *
     * @param array $decision
     * @return array
     * @throws RuntimeException
     */
    public static function validate(array $decision): array
    {
        self::assert($decision);
        return $decision;
    }

    /**
     * Required string assertion
     */
    private static function assertRequiredString(array $data, string $key): void
    {
        if (!array_key_exists($key, $data) || !is_string($data[$key])) {
            throw new RuntimeException(
                sprintf('Decision contract violation: "%s" must be a string', $key)
            );
        }
    }

    /**
     * Required float assertion
     */
    private static function assertRequiredFloat(array $data, string $key): void
    {
        if (!array_key_exists($key, $data) || !is_float($data[$key])) {
            throw new RuntimeException(
                sprintf('Decision contract violation: "%s" must be a float', $key)
            );
        }
    }

    /**
     * Required array assertion
     */
    private static function assertRequiredArray(array $data, string $key): void
    {
        if (!array_key_exists($key, $data) || !is_array($data[$key])) {
            throw new RuntimeException(
                sprintf('Decision contract violation: "%s" must be an array', $key)
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
                    'Decision contract violation: "%s" expected %s, got %s',
                    $label,
                    $expected,
                    $actual
                )
            );
        }
    }
}
