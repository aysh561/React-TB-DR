<?php
/**
 * Phase 16 — Anti-Cheat Behavioral Analyzer
 * File: /includes/phase-16-anti-cheat/intelligence/class-ac-behavior-analyzer.php
 *
 * ROLE (STRICT):
 * - Sirf behavioral consistency signals generate karna
 * - Koi decision, scoring, penalty, ya enforcement nahi
 * - No exceptions throw karni (safe intelligence layer)
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Behavior_Analyzer
{
    /**
     * Analyze behavioral consistency
     *
     * @param array $evidence Intelligence pipeline ka previous output
     * @return array Payload + signals.behavioral (copy-on-write)
     */
    public static function analyze(array $evidence): array
    {
        $signals = [];

        // Heuristic 1: Context round present but metadata bilkul static / minimal
        if (self::hasRoundContext($evidence) && self::hasStaticMetadata($evidence)) {
            $signals[] = 'static_metadata_across_round_context';
        }

        // Heuristic 2: Locale + screen dimensions ka rigid combination
        if (self::hasRigidLocaleScreenCombo($evidence)) {
            $signals[] = 'rigid_locale_screen_pattern';
        }

        // Heuristic 3: Synthetic signals already present but behavioral context unchanged
        if (
            !empty($evidence['signals']['synthetic']) &&
            self::hasUnchangedBehavioralSurface($evidence)
        ) {
            $signals[] = 'synthetic_flags_without_behavioral_variation';
        }

        // Copy-on-write
        $output = $evidence;

        if (!isset($output['signals']) || !is_array($output['signals'])) {
            $output['signals'] = [];
        }

        $output['signals']['behavioral'] = $signals;

        return $output;
    }

    /**
     * Check round-level context presence
     */
    private static function hasRoundContext(array $evidence): bool
    {
        return isset($evidence['context']['round']);
    }

    /**
     * Detect overly static / minimal metadata surface
     */
    private static function hasStaticMetadata(array $evidence): bool
    {
        if (!isset($evidence['metadata']) || !is_array($evidence['metadata'])) {
            return false;
        }

        $keys = array_keys($evidence['metadata']);

        // Bohat kam metadata fields hona suspicious stability ho sakti hai
        return count($keys) > 0 && count($keys) <= 2;
    }

    /**
     * Detect rigid locale + screen size combination
     */
    private static function hasRigidLocaleScreenCombo(array $evidence): bool
    {
        if (!isset($evidence['metadata']) || !is_array($evidence['metadata'])) {
            return false;
        }

        return
            isset(
                $evidence['metadata']['locale'],
                $evidence['metadata']['screen_width'],
                $evidence['metadata']['screen_height']
            ) &&
            is_string($evidence['metadata']['locale']) &&
            is_int($evidence['metadata']['screen_width']) &&
            is_int($evidence['metadata']['screen_height']);
    }

    /**
     * Detect lack of behavioral surface change when synthetic flags exist
     */
    private static function hasUnchangedBehavioralSurface(array $evidence): bool
    {
        if (!isset($evidence['metadata']) || !is_array($evidence['metadata'])) {
            return false;
        }

        // Agar metadata me koi dynamic-looking field hi nahi
        $dynamicKeys = ['timezone', 'os_version', 'app_version'];

        foreach ($dynamicKeys as $key) {
            if (isset($evidence['metadata'][$key])) {
                return false;
            }
        }

        return true;
    }
}
