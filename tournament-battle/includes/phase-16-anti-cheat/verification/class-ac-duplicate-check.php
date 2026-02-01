<?php
/**
 * Phase 16 — Anti-Cheat Duplicate Check
 * File: /includes/phase-16-anti-cheat/verification/class-ac-duplicate-check.php
 *
 * ROLE (STRICT):
 * - Sirf duplicate evidence detection
 * - Koi scoring, decision, penalty, inference nahi
 * - Fail-fast RuntimeException on duplicate
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Duplicate_Check
{
    /**
     * In-request idempotency registry
     * (No DB, no cache, sirf runtime memory)
     */
    private static array $seenSignatures = [];

    /**
     * Detect duplicate evidence
     *
     * @param array $evidence Validated intake payload
     * @return array Same payload if non-duplicate
     *
     * @throws RuntimeException
     */
    public static function verify(array $evidence): array
    {
        self::assertRequiredStructure($evidence);

        $signatureExact  = self::buildExactSignature($evidence);
        $signatureLogic  = self::buildLogicalSignature($evidence);

        if (isset(self::$seenSignatures[$signatureExact])) {
            throw new RuntimeException('Duplicate evidence detected (exact match)');
        }

        if (isset(self::$seenSignatures[$signatureLogic])) {
            throw new RuntimeException('Duplicate evidence detected (logical match)');
        }

        self::$seenSignatures[$signatureExact] = true;
        self::$seenSignatures[$signatureLogic] = true;

        return $evidence;
    }

    /**
     * Structural presence check
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    private static function assertRequiredStructure(array $evidence): void
    {
        if (
            !isset($evidence['file']) ||
            !is_array($evidence['file']) ||
            !isset($evidence['uploaded_at'])
        ) {
            throw new RuntimeException('Invalid evidence structure for duplicate check');
        }

        if (
            !isset($evidence['context']) ||
            !is_array($evidence['context'])
        ) {
            throw new RuntimeException('Context missing for duplicate check');
        }
    }

    /**
     * Exact duplicate signature
     * file.name + file.size + uploaded_at
     *
     * @param array $evidence
     * @return string
     */
    private static function buildExactSignature(array $evidence): string
    {
        $file = $evidence['file'];

        return implode('|', [
            (string) ($file['name'] ?? ''),
            (string) ($file['size'] ?? ''),
            (string) $evidence['uploaded_at'],
        ]);
    }

    /**
     * Logical duplicate signature
     * match_id + player_id + round
     *
     * @param array $evidence
     * @return string
     */
    private static function buildLogicalSignature(array $evidence): string
    {
        $context = $evidence['context'];

        return implode('|', [
            (string) ($context['match_id'] ?? ''),
            (string) ($context['player_id'] ?? ''),
            (string) ($context['round'] ?? ''),
        ]);
    }
}
