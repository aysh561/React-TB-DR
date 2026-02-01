<?php
/**
 * Phase 16 — Anti-Cheat Appeal Bundle (Governance)
 * File: /includes/phase-16-anti-cheat/governance/class-ac-appeal-bundle.php
 *
 * ROLE (STRICT):
 * - Sirf appeal-ready, read-only data bundle prepare karna
 * - Koi decision change, re-evaluation, penalty, ya enforcement nahi
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Appeal_Bundle
{
    /**
     * Prepare appeal bundle snapshot
     *
     * @param array $evidence Governance decision log ka output
     * @return array Payload + appeal.bundle
     *
     * @throws RuntimeException
     */
    public static function prepare(array $evidence): array
    {
        self::assertRequiredData($evidence);

        $bundle = [
            'decision' => [
                'state'     => (string)$evidence['decision']['state'],
                'score'     => (float)$evidence['decision']['score'],
                'reason'    => (string)$evidence['decision']['reason'],
                'breakdown' => $evidence['decision']['breakdown'],
            ],
            'governance' => [
                'log_id' => (string)$evidence['governance']['log_id'],
            ],
            'fusion' => [
                'total_score' => (float)$evidence['fusion']['total_score'],
                'breakdown'   => $evidence['fusion']['breakdown'],
            ],
            'signals' => [
                'synthetic'   => $evidence['signals']['synthetic']   ?? [],
                'behavioral'  => $evidence['signals']['behavioral']  ?? [],
                'cross_shot'  => $evidence['signals']['cross_shot']  ?? [],
            ],
            'timestamp' => gmdate('c'), // UTC ISO-8601
        ];

        // Immutable output
        $output = $evidence;
        $output['appeal']['bundle'] = $bundle;

        return $output;
    }

    /**
     * Required data presence validation
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    private static function assertRequiredData(array $evidence): void
    {
        if (
            !isset($evidence['decision']) ||
            !isset($evidence['governance']['log_id']) ||
            !isset($evidence['fusion']['total_score'], $evidence['fusion']['breakdown'])
        ) {
            throw new RuntimeException('Required data missing for appeal bundle preparation');
        }
    }
}
