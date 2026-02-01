<?php
/**
 * Phase 13.1.2 — Payout Queue Generator
 * Sirf payout queue banata hai, execution nahi.
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
            error_log('[RewardsPayoutQueue] Invalid tournament ID');
            return false;
        }

        // Idempotency guard
        $lock_key = '_tb_rewards_payout_queue_locked';
        if (get_post_meta($tournament_id, $lock_key, true)) {
            return true;
        }

        // Dependency validation
        $eligible_players = get_post_meta($tournament_id, '_tb_rewards_eligible_players', true);
        $prize_snapshot   = get_post_meta($tournament_id, '_tb_rewards_prize_snapshot', true);

        if (!is_array($eligible_players) || empty($eligible_players)) {
            error_log('[RewardsPayoutQueue] Eligible players missing');
            return false;
        }

        if (!is_array($prize_snapshot) || empty($prize_snapshot)) {
            error_log('[RewardsPayoutQueue] Prize snapshot missing');
            return false;
        }

        // Existing queue for duplicate protection
        $existing_queue = get_post_meta($tournament_id, '_tb_rewards_payout_queue', true);
        if (!is_array($existing_queue)) {
            $existing_queue = [];
        }

        $queue_index = [];
        foreach ($existing_queue as $item) {
            if (!empty($item['player_id'])) {
                $queue_index[$item['player_id']] = true;
            }
        }

        $new_queue_items = [];
        $created_at = time();

        foreach ($eligible_players as $position => $player_id) {

            $player_id = intval($player_id);
            if ($player_id <= 0) {
                continue;
            }

            // Duplicate protection
            if (isset($queue_index[$player_id])) {
                continue;
            }

            if (!isset($prize_snapshot[$position])) {
                continue;
            }

            $amount = $prize_snapshot[$position];
            if (!is_numeric($amount)) {
                error_log('[RewardsPayoutQueue] Invalid prize amount for position ' . $position);
                return false;
            }

            $new_queue_items[] = [
                'tournament_id' => $tournament_id,
                'player_id'     => $player_id,
                'position'      => $position,
                'amount'        => (float) $amount,
                'status'        => 'pending',
                'created_at'    => $created_at,
            ];
        }

        if (empty($existing_queue) && empty($new_queue_items)) {
            error_log('[RewardsPayoutQueue] No payout queue items generated');
            return false;
        }

        // Persist append-only
        if (!empty($new_queue_items)) {
            foreach ($new_queue_items as $item) {
                add_post_meta($tournament_id, '_tb_rewards_payout_queue', $item);
            }
        }

        add_post_meta($tournament_id, '_tb_rewards_payout_queue_created_at', $created_at);
        add_post_meta($tournament_id, $lock_key, 1);

        return true;
    }
}
