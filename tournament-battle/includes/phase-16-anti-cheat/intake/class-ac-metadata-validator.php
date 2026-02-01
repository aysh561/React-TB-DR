<?php
/**
 * Phase 16 — Anti-Cheat Metadata Validator
 * File: /includes/phase-16-anti-cheat/intake/class-ac-metadata-validator.php
 *
 * ROLE (STRICT):
 * - Sirf metadata validation
 * - Na normalization, na detection, na scoring, na decision logic
 * - Fail-fast validation with RuntimeException
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Metadata_Validator
{
    /**
     * Validate normalized evidence metadata
     *
     * @param array $evidence Normalized evidence payload
     * @return array Same evidence payload (immutable)
     *
     * @throws RuntimeException
     */
    public static function validate(array $evidence): array
    {
        if (!array_key_exists('metadata', $evidence)) {
            throw new RuntimeException('Metadata missing in evidence payload');
        }

        if (!is_array($evidence['metadata'])) {
            throw new RuntimeException('Metadata must be an array');
        }

        $metadata = $evidence['metadata'];

        self::validateTypes($metadata);
        self::validateLogicalSanity($metadata);

        // Immutable output — same payload return
        return $evidence;
    }

    /**
     * Type-level validation
     *
     * @param array $metadata
     * @throws RuntimeException
     */
    private static function validateTypes(array $metadata): void
    {
        if (isset($metadata['screen_width']) && !self::isPositiveInt($metadata['screen_width'])) {
            throw new RuntimeException('screen_width must be a positive integer');
        }

        if (isset($metadata['screen_height']) && !self::isPositiveInt($metadata['screen_height'])) {
            throw new RuntimeException('screen_height must be a positive integer');
        }

        $stringKeys = ['timezone', 'locale', 'os', 'app_version'];

        foreach ($stringKeys as $key) {
            if (isset($metadata[$key]) && !self::isNonEmptyString($metadata[$key])) {
                throw new RuntimeException($key . ' must be a non-empty string');
            }
        }
    }

    /**
     * Basic logical sanity checks
     *
     * @param array $metadata
     * @throws RuntimeException
     */
    private static function validateLogicalSanity(array $metadata): void
    {
        if (isset($metadata['screen_width'], $metadata['screen_height'])) {
            $width  = (int) $metadata['screen_width'];
            $height = (int) $metadata['screen_height'];

            if ($width <= 0 || $height <= 0) {
                throw new RuntimeException('Screen dimensions must be greater than zero');
            }

            if (($width * $height) <= 0) {
                throw new RuntimeException('Invalid screen resolution sanity');
            }
        }
    }

    /**
     * Check positive integer
     *
     * @param mixed $value
     * @return bool
     */
    private static function isPositiveInt($value): bool
    {
        return is_int($value) && $value > 0;
    }

    /**
     * Check non-empty string
     *
     * @param mixed $value
     * @return bool
     */
    private static function isNonEmptyString($value): bool
    {
        return is_string($value) && trim($value) !== '';
    }
}
