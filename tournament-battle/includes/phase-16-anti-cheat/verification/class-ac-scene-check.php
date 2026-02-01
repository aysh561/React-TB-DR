```php
<?php
/**
 * Phase 16 — Anti-Cheat Scene Consistency Check
 * File: /includes/phase-16-anti-cheat/verification/class-ac-scene-check.php
 *
 * ROLE (STRICT):
 * - Sirf scene-level consistency verification
 * - Koi scoring, decision, penalty, ya intelligence logic nahi
 * - Fail-fast RuntimeException on scene inconsistency
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Scene_Check
{
    /**
     * Verify scene-level consistency
     *
     * @param array $evidence Previous verification pipeline output
     * @return array Same payload if valid
     *
     * @throws RuntimeException
     */
    public static function verify(array $evidence): array
    {
        self::validateScreenDimensions($evidence);
        self::validateFileType($evidence);
        self::validateContextScene($evidence);

        // Immutable flow — payload as-is return
        return $evidence;
    }

    /**
     * Screen dimension consistency
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    private static function validateScreenDimensions(array $evidence): void
    {
        if (!isset($evidence['metadata']) || !is_array($evidence['metadata'])) {
            return;
        }

        $meta = $evidence['metadata'];

        if (isset($meta['screen_width']) || isset($meta['screen_height'])) {
            if (!isset($meta['screen_width'], $meta['screen_height'])) {
                throw new RuntimeException('Incomplete screen dimension metadata');
            }

            if (!self::isPositiveInt($meta['screen_width']) || !self::isPositiveInt($meta['screen_height'])) {
                throw new RuntimeException('Screen dimensions must be positive integers');
            }

            if ((int)$meta['screen_width'] === 0 || (int)$meta['screen_height'] === 0) {
                throw new RuntimeException('Screen dimension cannot be zero');
            }
        }
    }

    /**
     * File type vs scene sanity
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    private static function validateFileType(array $evidence): void
    {
        if (!isset($evidence['file']) || !is_array($evidence['file'])) {
            return;
        }

        if (!isset($evidence['file']['type'])) {
            return;
        }

        if (!is_string($evidence['file']['type']) || trim($evidence['file']['type']) === '') {
            throw new RuntimeException('file.type must be a non-empty string');
        }

        if (strpos($evidence['file']['type'], 'image/') !== 0) {
            throw new RuntimeException('Non-image file type not allowed for scene evidence');
        }
    }

    /**
     * Basic contextual scene sanity
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    private static function validateContextScene(array $evidence): void
    {
        if (!isset($evidence['context']) || !is_array($evidence['context'])) {
            return;
        }

        $contextKeys = ['round', 'match_id'];

        foreach ($contextKeys as $key) {
            if (isset($evidence['context'][$key])) {
                $value = $evidence['context'][$key];

                if (!is_scalar($value) || trim((string)$value) === '') {
                    throw new RuntimeException('Invalid context value for ' . $key);
                }
            }
        }
    }

    /**
     * Positive integer check
     *
     * @param mixed $value
     * @return bool
     */
    private static function isPositiveInt($value): bool
    {
        return is_int($value) && $value > 0;
    }
}
