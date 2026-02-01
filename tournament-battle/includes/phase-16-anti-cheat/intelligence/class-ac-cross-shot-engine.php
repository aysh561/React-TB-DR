<?php
/**
 * Phase 16 — Anti-Cheat Cross-Shot Consistency Engine
 * File: /includes/phase-16-anti-cheat/intelligence/class-ac-cross-shot-engine.php
 *
 * ROLE (STRICT):
 * - Sirf cross-shot consistency signals generate karna
 * - Koi decision, scoring, penalty, ya enforcement nahi
 * - No exceptions (safe intelligence layer)
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Cross_Shot_Engine
{
    /**
     * Runtime in-memory registry for previous shots
     * (No DB, no cache — sirf request lifecycle)
     */
    private static array $history = [];

    /**
     * Analyze cross-shot consistency
     *
     * @param array $evidence Intelligence pipeline ka previous output
     * @return array Payload + signals.cross_shot (copy-on-write)
     */
    public static function analyze(array $evidence): array
    {
        $signals = [];

        $contextKey = self::buildContextKey($evidence);

        if ($contextKey !== null && isset(self::$history[$contextKey])) {
            $previous = self::$history[$contextKey];

            // Heuristic 1: Screen dimension ka sudden unrealistic change
            if (self::screenDimensionChanged($previous, $evidence)) {
                $signals[] = 'sudden_screen_dimension_change';
            }

            // Heuristic 2: Locale ka abrupt switch
            if (self::localeSwitched($previous, $evidence)) {
                $signals[] = 'abrupt_locale_switch';
            }

            // Heuristic 3: Metadata surface present vs missing flip
            if (self::metadataSurfaceFlipped($previous, $evidence)) {
                $signals[] = 'metadata_presence_flip_across_shots';
            }
        }

        // Current evidence ko history me store karna (immutable reference)
        if ($contextKey !== null) {
            self::$history[$contextKey] = $evidence;
        }

        // Copy-on-write
        $output = $evidence;

        if (!isset($output['signals']) || !is_array($output['signals'])) {
            $output['signals'] = [];
        }

        $output['signals']['cross_shot'] = $signals;

        return $output;
    }

    /**
     * Build context key (player + match)
     */
    private static function buildContextKey(array $evidence): ?string
    {
        if (
            !isset($evidence['context']['player_id']) ||
            !isset($evidence['context']['match_id'])
        ) {
            return null;
        }

        return (string)$evidence['context']['player_id'] . '|' .
               (string)$evidence['context']['match_id'];
    }

    /**
     * Detect screen dimension change
     */
    private static function screenDimensionChanged(array $prev, array $curr): bool
    {
        if (
            !isset(
                $prev['metadata']['screen_width'],
                $prev['metadata']['screen_height'],
                $curr['metadata']['screen_width'],
                $curr['metadata']['screen_height']
            )
        ) {
            return false;
        }

        return
            $prev['metadata']['screen_width'] !== $curr['metadata']['screen_width'] ||
            $prev['metadata']['screen_height'] !== $curr['metadata']['screen_height'];
    }

    /**
     * Detect locale switch
     */
    private static function localeSwitched(array $prev, array $curr): bool
    {
        if (
            !isset($prev['metadata']['locale']) ||
            !isset($curr['metadata']['locale'])
        ) {
            return false;
        }

        return $prev['metadata']['locale'] !== $curr['metadata']['locale'];
    }

    /**
     * Detect metadata present vs missing flip
     */
    private static function metadataSurfaceFlipped(array $prev, array $curr): bool
    {
        $prevHasMeta = isset($prev['metadata']) && !empty($prev['metadata']);
        $currHasMeta = isset($curr['metadata']) && !empty($curr['metadata']);

        return $prevHasMeta !== $currHasMeta;
    }
}
