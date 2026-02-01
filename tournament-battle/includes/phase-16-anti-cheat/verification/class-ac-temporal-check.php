<?php
/**
 * Phase 16 — Anti-Cheat Temporal Consistency Check
 * File: /includes/phase-16-anti-cheat/verification/class-ac-temporal-check.php
 *
 * ROLE (STRICT):
 * - Sirf temporal consistency verification
 * - Koi scoring, decision, penalty, ya inference nahi
 * - Fail-fast RuntimeException on invalid temporal state
 */

if (!defined('ABSPATH')) {
    exit;
}

final class AC_Temporal_Check
{
    /**
     * Max allowed future drift (seconds)
     * 5 minutes grace window
     */
    private const FUTURE_GRACE_SECONDS = 300;

    /**
     * Verify temporal consistency
     *
     * @param array $evidence Previous verification pipeline output
     * @return array Same payload if valid
     *
     * @throws RuntimeException
     */
    public static function verify(array $evidence): array
    {
        self::assertUploadedAt($evidence);

        $uploadedAtTs = self::parseIsoTimestamp($evidence['uploaded_at']);

        self::validateNotFarFuture($uploadedAtTs);

        if (isset($evidence['context']) && is_array($evidence['context'])) {
            self::validateContextualTime($uploadedAtTs, $evidence['context']);
        }

        // Immutable flow — same payload return
        return $evidence;
    }

    /**
     * uploaded_at presence & type check
     *
     * @param array $evidence
     * @throws RuntimeException
     */
    private static function assertUploadedAt(array $evidence): void
    {
        if (!isset($evidence['uploaded_at'])) {
            throw new RuntimeException('uploaded_at missing in evidence');
        }

        if (!is_string($evidence['uploaded_at']) || trim($evidence['uploaded_at']) === '') {
            throw new RuntimeException('uploaded_at must be a non-empty string');
        }
    }

    /**
     * ISO-8601 compatible timestamp parse (string-level validation)
     *
     * @param string $value
     * @return int
     * @throws RuntimeException
     */
    private static function parseIsoTimestamp(string $value): int
    {
        $timestamp = strtotime($value);

        if ($timestamp === false) {
            throw new RuntimeException('uploaded_at is not a valid ISO-8601 timestamp');
        }

        if ($timestamp < 0) {
            throw new RuntimeException('uploaded_at timestamp is negative or malformed');
        }

        return $timestamp;
    }

    /**
     * uploaded_at future sanity check
     *
     * @param int $uploadedAtTs
     * @throws RuntimeException
     */
    private static function validateNotFarFuture(int $uploadedAtTs): void
    {
        $now = time();

        if ($uploadedAtTs > ($now + self::FUTURE_GRACE_SECONDS)) {
            throw new RuntimeException('uploaded_at is too far in the future');
        }
    }

    /**
     * Basic contextual temporal consistency
     *
     * Agar context me round_time ho aur valid ho:
     * uploaded_at us se pehle nahi hona chahiye
     *
     * @param int $uploadedAtTs
     * @param array $context
     * @throws RuntimeException
     */
    private static function validateContextualTime(int $uploadedAtTs, array $context): void
    {
        if (!isset($context['round_time'])) {
            return;
        }

        if (!is_string($context['round_time']) || trim($context['round_time']) === '') {
            throw new RuntimeException('context.round_time must be a non-empty string if present');
        }

        $roundTimeTs = strtotime($context['round_time']);

        if ($roundTimeTs === false || $roundTimeTs < 0) {
            throw new RuntimeException('context.round_time is not a valid timestamp');
        }

        if ($uploadedAtTs < $roundTimeTs) {
            throw new RuntimeException('uploaded_at cannot be earlier than context.round_time');
        }
    }
}
