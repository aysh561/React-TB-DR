<?php
/**
 * Phase 16 — Anti-Cheat Evidence Normalizer
 * File: /includes/phase-16-anti-cheat/intake/class-ac-evidence-normalizer.php
 *
 * ROLE (STRICT):
 * - Raw evidence payload ko normalize karna
 * - Sirf structure cleaning aur key whitelisting
 * - Koi verification, detection, scoring, hashing, OCR, ya DB write nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Evidence_Normalizer
{
    /**
     * Whitelisted top-level keys jo loader se accept ki ja sakti hain
     */
    private const ALLOWED_KEYS = [
        'source',
        'uploaded_at',
        'file',
        'metadata',
        'context',
    ];

    /**
     * File object ke allowed keys
     */
    private const FILE_KEYS = [
        'name',
        'type',
        'size',
        'tmp_name',
        'error',
    ];

    /**
     * Metadata ke allowed keys
     */
    private const METADATA_KEYS = [
        'device',
        'os',
        'os_version',
        'app_version',
        'screen_width',
        'screen_height',
        'timezone',
        'locale',
    ];

    /**
     * Context ke allowed keys
     */
    private const CONTEXT_KEYS = [
        'tournament_id',
        'match_id',
        'player_id',
        'round',
    ];

    /**
     * Normalize raw evidence payload
     *
     * @param array $raw
     * @return array Normalized evidence (contract-compatible)
     */
    public static function normalize(array $raw): array
    {
        $normalized = [];

        foreach (self::ALLOWED_KEYS as $key) {
            if (!array_key_exists($key, $raw)) {
                continue;
            }

            switch ($key) {
                case 'file':
                    if (is_array($raw['file'])) {
                        $normalized['file'] = self::filterKeys(
                            $raw['file'],
                            self::FILE_KEYS
                        );
                    }
                    break;

                case 'metadata':
                    if (is_array($raw['metadata'])) {
                        $normalized['metadata'] = self::filterKeys(
                            $raw['metadata'],
                            self::METADATA_KEYS
                        );
                    }
                    break;

                case 'context':
                    if (is_array($raw['context'])) {
                        $normalized['context'] = self::filterKeys(
                            $raw['context'],
                            self::CONTEXT_KEYS
                        );
                    }
                    break;

                default:
                    $normalized[$key] = $raw[$key];
            }
        }

        return self::applyDefaults($normalized);
    }

    /**
     * Sirf allowed keys retain karta hai
     *
     * @param array $data
     * @param array $allowedKeys
     * @return array
     */
    private static function filterKeys(array $data, array $allowedKeys): array
    {
        $filtered = [];

        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $data)) {
                $filtered[$key] = $data[$key];
            }
        }

        return $filtered;
    }

    /**
     * Contract ke mutabiq required structural defaults apply karta hai
     *
     * @param array $evidence
     * @return array
     */
    private static function applyDefaults(array $evidence): array
    {
        if (!isset($evidence['metadata'])) {
            $evidence['metadata'] = [];
        }

        if (!isset($evidence['context'])) {
            $evidence['context'] = [];
        }

        if (!isset($evidence['uploaded_at'])) {
            $evidence['uploaded_at'] = gmdate('c');
        }

        return $evidence;
    }
}
