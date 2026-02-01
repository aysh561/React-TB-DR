<?php
/**
 * Rewards Loader — Phase 13 Central Orchestrator
 * Safety-only coordinator. No business logic.
 */

if (php_sapi_name() !== 'cli' && !defined('WPINC')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

if (!defined('TB_REWARDS_LOADER_LOADED')) {
    define('TB_REWARDS_LOADER_LOADED', true);
}

if (!function_exists('run_tournament_rewards_engine')) {

    function run_tournament_rewards_engine($tournament_id)
    {
        // --------- Hard validation ----------
        if (empty($tournament_id)) {
            error_log('[RewardsEngine] Tournament ID missing');
            return false;
        }

        $tournament_id = intval($tournament_id);
        if ($tournament_id <= 0) {
            error_log('[RewardsEngine] Invalid Tournament ID');
            return false;
        }

        // --------- Idempotency keys ----------
        $meta_key_completed = '_tb_rewards_completed';
        $meta_key_state     = '_tb_rewards_state';
        $meta_key_lock      = '_tb_rewards_lock';

        // --------- Completed guard ----------
        if (get_post_meta($tournament_id, $meta_key_completed, true)) {
            // Already completed — safe skip
            return true;
        }

        // --------- Re-entry lock ----------
        if (get_post_meta($tournament_id, $meta_key_lock, true)) {
            // Another process already running
            return false;
        }

        update_post_meta($tournament_id, $meta_key_lock, time());

        // --------- Load steps (LOCKED ORDER) ----------
        $steps = [
            'step-13.1-eligibility-check.php',
            'step-13.1.1-prize-model.php',
            'step-13.1.2-payout-queue.php',
            'step-13.1.3-payout-execution.php',
            'step-13.1.4-wallet-ledger.php',
            'step-13.1.5-admin-audit-log.php',
        ];

        $base_dir = __DIR__ . '/';

        $current_state = get_post_meta($tournament_id, $meta_key_state, true);
        $resume_index  = is_numeric($current_state) ? intval($current_state) : 0;

        foreach ($steps as $index => $file) {

            if ($index < $resume_index) {
                continue; // already executed
            }

            $path = $base_dir . $file;

            if (!file_exists($path)) {
                error_log('[RewardsEngine] Missing step file: ' . $file);
                update_post_meta($tournament_id, $meta_key_state, $index);
                delete_post_meta($tournament_id, $meta_key_lock);
                return false;
            }

            require_once $path;

            if (!function_exists('tb_rewards_step_execute')) {
                error_log('[RewardsEngine] Step executor not found in ' . $file);
                update_post_meta($tournament_id, $meta_key_state, $index);
                delete_post_meta($tournament_id, $meta_key_lock);
                return false;
            }

            try {
                $result = tb_rewards_step_execute($tournament_id);

                if ($result !== true) {
                    error_log('[RewardsEngine] Step failed: ' . $file);
                    update_post_meta($tournament_id, $meta_key_state, $index);
                    delete_post_meta($tournament_id, $meta_key_lock);
                    return false;
                }

                update_post_meta($tournament_id, $meta_key_state, $index + 1);

            } catch (Throwable $e) {
                error_log('[RewardsEngine] Exception in ' . $file . ' — ' . $e->getMessage());
                update_post_meta($tournament_id, $meta_key_state, $index);
                delete_post_meta($tournament_id, $meta_key_lock);
                return false;
            }
        }

        // --------- Mark completed ----------
        update_post_meta($tournament_id, $meta_key_completed, time());
        delete_post_meta($tournament_id, $meta_key_lock);

        return true;
    }
}
