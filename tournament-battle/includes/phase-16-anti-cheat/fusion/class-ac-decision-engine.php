<?php
/**
 * Phase 16 — Anti-Cheat Decision Engine
 * File: /includes/phase-16-anti-cheat/fusion/class-ac-decision-engine.php
 *
 * ROLE (STRICT):
 * - Sirf fusion output interpret karna
 * - Deterministic decision object generate karna
 * - Koi penalty, enforcement, ya governance action nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Decision_Engine
{
    /**
     * Fixed, explainable thresholds
     */
    private const THRESHOLD_ALLOW    = 2.0;
    private const THRESHOLD_FLAG     = 5.0;
    // >= THRESHOLD_FLAG => ESCALATE

    /**
     * Generate decision object from fusion output
     *
     * @param array $evidence Fusion pipeline ka output
     * @return array Payload + decision block (immutable)
     *
     * @throws RuntimeException
     */
    public static function decide(array $evidence): array
    {
        if (
            !isset($evidence['fusion']) ||
            !is_array($evidence['fusion']) ||
            !isset($evidence['fusion']['total_score'], $evidence['fusion']['breakdown'])
        ) {
            throw new RuntimeException('Fusion data missing for decision engine');
        }

        $totalScore = $evidence['fusion']['total_score'];
        $breakdown  = $evidence['fusion']['breakdown'];

        if (!is_numeric($totalScore) || !is_array($breakdown)) {
            throw new RuntimeException('Invalid fusion data structure');
        }

        [$state, $reason] = self::mapDecisionState((float)$totalScore);

        $decision = [
            'state'     => $state,
            'reason'    => $reason,
            'score'     => (float)$totalScore,
            'breakdown' => self::buildDecisionBreakdown($breakdown),
        ];

        // Immutable output
        $output = $evidence;
        $output['decision'] = $decision;

        return $output;
    }

    /**
     * Deterministic decision mapping
     *
     * @param float $score
     * @return array [state, reason]
     */
    private static function mapDecisionState(float $score): array
    {
        if ($score <= self::THRESHOLD_ALLOW) {
            return [
                'ALLOW',
                'Low risk score within safe threshold',
            ];
        }

        if ($score <= self::THRESHOLD_FLAG) {
            return [
                'FLAG',
                'Moderate risk score requires review',
            ];
        }

        return [
            'ESCALATE',
            'High risk score requires manual attention',
        ];
    }

    /**
     * Build explainable decision breakdown
     *
     * @param array $fusionBreakdown
     * @return array
     */
    private static function buildDecisionBreakdown(array $fusionBreakdown): array
    {
        $summary = [];

        foreach ($fusionBreakdown as $category => $data) {
            if (!is_array($data)) {
                continue;
            }

            $summary[$category] = [
                'signal_count' => (int)($data['count'] ?? 0),
                'weight'       => (float)($data['weight'] ?? 0),
                'score'        => (float)($data['score'] ?? 0),
                'signals'      => array_values($data['signals'] ?? []),
            ];
        }

        return $summary;
    }
}
