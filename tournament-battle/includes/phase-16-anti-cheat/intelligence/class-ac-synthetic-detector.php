<?php
/**
 * Phase 16 — Anti-Cheat Synthetic Detector
 * File: /includes/phase-16-anti-cheat/intelligence/class-ac-synthetic-detector.php
 *
 * ROLE (STRICT):
 * - Sirf synthetic / fabricated evidence ke low-level indicators generate karna
 * - Koi decision, scoring, penalty, ya enforcement nahi
 * - No exceptions throw karni (safe intelligence layer)
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Synthetic_Detector
{
    /**
     * Generate synthetic indicators
     *
     * @param array $evidence Verified evidence payload
     * @return array Payload + signals.synthetic (copy-on-write)
     */
    public static function analyze(array $evidence): array
    {
        $signals = [];

        // Heuristic 1: Screenshot context hai lekin file missing
        if (self::hasScreenshotContext($evidence) && empty($evidence['file'])) {
            $signals[] = 'missing_file_with_screenshot_context';
        }

        // Heuristic 2: Screen dimensions present but file type missing
        if (
            isset($evidence['metadata']['screen_width'], $evidence['metadata']['screen_height']) &&
            empty($evidence['file']['type'])
        ) {
            $signals[] = 'screen_dimensions_without_file_type';
        }

        // Heuristic 3: Static / placeholder-like metadata values
        if (self::hasPlaceholderMetadata($evidence)) {
            $signals[] = 'placeholder_metadata_detected';
        }

        // Heuristic 4: Image file type but zero / missing dimensions
        if (
            isset($evidence['file']['type']) &&
            is_string($evidence['file']['type']) &&
            strpos($evidence['file']['type'], 'image/') === 0 &&
            (
                empty($evidence['metadata']['screen_width']) ||
                empty($evidence['metadata']['screen_height'])
            )
        ) {
            $signals[] = 'image_without_screen_dimensions';
        }

        // Copy-on-write: original evidence untouched
        $output = $evidence;

        if (!isset($output['signals']) || !is_array($output['signals'])) {
            $output['signals'] = [];
        }

        $output['signals']['synthetic'] = $signals;

        return $output;
    }

    /**
     * Check screenshot-like context presence
     *
     * @param array $evidence
     * @return bool
     */
    private static function hasScreenshotContext(array $evidence): bool
    {
        if (!isset($evidence['context']) || !is_array($evidence['context'])) {
            return false;
        }

        return isset($evidence['context']['match_id']) || isset($evidence['context']['round']);
    }

    /**
     * Detect static / placeholder metadata patterns
     *
     * @param array $evidence
     * @return bool
     */
    private static function hasPlaceholderMetadata(array $evidence): bool
    {
        if (!isset($evidence['metadata']) || !is_array($evidence['metadata'])) {
            return false;
        }

        $placeholders = ['unknown', 'default', 'n/a', 'na', 'test'];

        foreach ($evidence['metadata'] as $value) {
            if (!is_string($value)) {
                continue;
            }

            $normalized = strtolower(trim($value));

            if (in_array($normalized, $placeholders, true)) {
                return true;
            }
        }

        return false;
    }
}
