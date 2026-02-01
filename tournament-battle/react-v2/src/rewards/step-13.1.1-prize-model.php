<?php
/**
 * Phase 13.1.1 — Prize Model Lock
 * Final, immutable prize structure freeze step.
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
            error_log('[RewardsPrizeModel] Invalid tournament ID');
            return false;
        }

        // Idempotency guard
        $lock_key = '_tb_rewards_prize_model_locked';
        if (get_post_meta($tournament_id, $lock_key, true)) {
            return true;
        }

        // Eligibility dependency
        $eligible_players = get_post_meta($tournament_id, '_tb_rewards_eligible_players', true);
        if (!is_array($eligible_players) || empty($eligible_players)) {
            error_log('[RewardsPrizeModel] Eligible players missing or empty');
            return false;
        }

        // Prize configuration source
        $prize_model = get_post_meta($tournament_id, '_tb_prize_model', true);
        if (!is_array($prize_model) || empty($prize_model['type'])) {
            error_log('[RewardsPrizeModel] Prize model configuration missing');
            return false;
        }

        $type = $prize_model['type'];
        $final_snapshot = [];

        // Fixed amount model
        if ($type === 'fixed_amount') {

            if (empty($prize_model['positions']) || !is_array($prize_model['positions'])) {
                error_log('[RewardsPrizeModel] Invalid fixed_amount prize structure');
                return false;
            }

            foreach ($prize_model['positions'] as $position => $amount) {
                if (!is_numeric($amount)) {
                    error_log('[RewardsPrizeModel] Invalid prize amount for position ' . $position);
                    return false;
                }
                $final_snapshot[$position] = (float) $amount;
            }

        // Percentage split model
        } elseif ($type === 'percentage_split') {

            if (
                empty($prize_model['total_pool']) ||
                !is_numeric($prize_model['total_pool']) ||
                empty($prize_model['percentages']) ||
                !is_array($prize_model['percentages'])
            ) {
                error_log('[RewardsPrizeModel] Invalid percentage_split configuration');
                return false;
            }

            $total_pool = (float) $prize_model['total_pool'];

            foreach ($prize_model['percentages'] as $position => $percent) {
                if (!is_numeric($percent)) {
                    error_log('[RewardsPrizeModel] Invalid percentage for position ' . $position);
                    return false;
                }
                $final_snapshot[$position] = ($total_pool * ((float) $percent)) / 100;
            }

        } else {
            error_log('[RewardsPrizeModel] Unsupported prize model type');
            return false;
        }

        if (empty($final_snapshot)) {
            error_log('[RewardsPrizeModel] Final prize snapshot empty');
            return false;
        }

        // Persist immutable snapshot — append only
        add_post_meta($tournament_id, '_tb_rewards_prize_snapshot', $final_snapshot);
        add_post_meta($tournament_id, '_tb_rewards_prize_locked_at', time());
        add_post_meta($tournament_id, '_tb_rewards_prize_eligibility_ref', time());
        add_post_meta($tournament_id, $lock_key, 1);

        return true;
    }
}
