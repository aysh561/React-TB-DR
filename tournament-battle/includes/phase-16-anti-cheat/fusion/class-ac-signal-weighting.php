<?php
/**
 * Phase 16 — Anti-Cheat Signal Weighting & Fusion
 * File: /includes/phase-16-anti-cheat/fusion/class-ac-signal-weighting.php
 *
 * ROLE (STRICT):
 * - Sirf signals ko weight aur aggregate karna
 * - Koi final decision, penalty, enforcement, ya governance nahi
 * - Safe fusion layer (no exceptions)
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Signal_Weighting
{
    /**
     * Fixed, explainable signal weights
     * Deterministic mapping — no ML, no probability
     */
    private const WEIGHTS = [
        'synthetic' => 1.0,   // low–medium
        'behavioral' => 2.0,  // medium
        'cross_shot' => 3.0,  // medium–high
    ];

    /**
     * Fuse and weight intelligence signals
     *
     * @param array $evidence Intelligence pipeline ka output
     * @return array Fusion-ready payload (immutable)
     */
    public static function fuse(array $evidence): array
    {
        $signals = $evidence['signals'] ?? [];

        $synthetic   = is_array($signals['synthetic']   ?? null) ? $signals['synthetic']   : [];
        $behavioral  = is_array($signals['behavioral']  ?? null) ? $signals['behavioral']  : [];
        $crossShot   = is_array($signals['cross_shot']  ?? null) ? $signals['cross_shot']  : [];

        $breakdown = [
            'synthetic' => self::buildBreakdown('synthetic', $synthetic),
            'behavioral' => self::buildBreakdown('behavioral', $behavioral),
            'cross_shot' => self::buildBreakdown('cross_shot', $crossShot),
        ];

        $totalScore =
            $breakdown['synthetic']['score'] +
            $breakdown['behavioral']['score'] +
            $breakdown['cross_shot']['score'];

        // Copy-on-write — original evidence untouched
        $output = $evidence;

        $output['fusion'] = [
            'weights' => self::WEIGHTS,
            'total_score' => $totalScore,
            'breakdown' => $breakdown,
        ];

        return $output;
    }

    /**
     * Category-wise breakdown builder
     *
     * @param string $type
     * @param array $signals
     * @return array
     */
    private static function buildBreakdown(string $type, array $signals): array
    {
        $weight = self::WEIGHTS[$type] ?? 0.0;
        $count  = count($signals);

        return [
            'signals' => array_values($signals),
            'count'   => $count,
            'weight'  => $weight,
            'score'   => $count * $weight,
        ];
    }
}
