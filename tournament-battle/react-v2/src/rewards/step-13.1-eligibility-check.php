<?php
/**
 * Phase 13.1 — Eligibility Check
 * Strict eligibility extraction based on final verified results.
 */

if (php_sapi_name() !== 'cli' && !defined('WPINC')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

if (!function_exists('tb_rewards_step_execute')) {

    function tb_rewards_step_execute($tournament_id)
    {
        $tournament_id = intval($tournament_id);
        if ($tournament_id <= 0) {
            error_log('[RewardsEligibility] Invalid tournament ID');
            return false;
        }

        // Idempotency — agar eligibility pehle calculate ho chuki ho
        $eligibility_key = '_tb_rewards_eligibility_locked';
        if (get_post_meta($tournament_id, $eligibility_key, true)) {
            return true;
        }

        // Tournament status verify
        $status = get_post_meta($tournament_id, '_tb_tournament_status', true);
        if ($status !== 'completed') {
            error_log('[RewardsEligibility] Tournament not completed');
            return false;
        }

        // Final match existence
        $final_match_id = get_post_meta($tournament_id, '_tb_final_match_id', true);
        if (empty($final_match_id)) {
            error_log('[RewardsEligibility] Final match missing');
            return false;
        }

        // Digital Referee verdict
        $verdict = get_post_meta($final_match_id, '_tb_referee_verdict', true);
        if ($verdict !== 'approved') {
            error_log('[RewardsEligibility] Referee verdict not approved');
            return false;
        }

        // Fetch final results (already verified by referee system)
        $final_results = get_post_meta($final_match_id, '_tb_final_results', true);
        if (!is_array($final_results) || empty($final_results)) {
            error_log('[RewardsEligibility] Final results missing');
            return false;
        }

        $eligible_players = [];

        foreach ($final_results as $entry) {

            if (
                empty($entry['player_id']) ||
                empty($entry['position']) ||
                !empty($entry['disqualified']) ||
                !empty($entry['rejected'])
            ) {
                continue;
            }

            $player_id = intval($entry['player_id']);
            if ($player_id <= 0) {
                continue;
            }

            // Duplicate protection
            if (in_array($player_id, $eligible_players, true)) {
                continue;
            }

            $eligible_players[] = $player_id;
        }

        if (empty($eligible_players)) {
            error_log('[RewardsEligibility] No eligible players found');
            return false;
        }

        // Persist eligibility — append only
        add_post_meta($tournament_id, '_tb_rewards_eligible_players', $eligible_players);
        add_post_meta($tournament_id, '_tb_rewards_eligibility_timestamp', time());
        add_post_meta($tournament_id, $eligibility_key, 1);

        return true;
    }
}
