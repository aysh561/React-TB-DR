<?php
/**
 * Phase 13.1.3 — Payout Execution
 * Queue se read karke logical execution record karta hai.
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
            error_log('[RewardsPayoutExecution] Invalid tournament ID');
            return false;
        }

        // Idempotency guard
        $lock_key = '_tb_rewards_payout_execution_locked';
        if (get_post_meta($tournament_id, $lock_key, true)) {
            return true;
        }

        // Dependency validation
        $queue_items = get_post_meta($tournament_id, '_tb_rewards_payout_queue', false);
        if (!is_array($queue_items) || empty($queue_items)) {
            error_log('[RewardsPayoutExecution] Payout queue missing or empty');
            return false;
        }

        $executed_any = false;

        foreach ($queue_items as $item) {

            if (!is_array($item)) {
                continue;
            }

            // Per-item idempotency
            if (!empty($item['execution_status'])) {
                continue;
            }

            if (empty($item['status']) || $item['status'] !== 'pending') {
                continue;
            }

            $execution_result = 'completed';
            $execution_note   = 'Execution flag recorded';
            $executed_at      = time();

            try {
                // Logical execution only — no wallet / no API
                add_post_meta(
                    $tournament_id,
                    '_tb_rewards_payout_execution_record',
                    [
                        'tournament_id'    => $tournament_id,
                        'player_id'        => isset($item['player_id']) ? intval($item['player_id']) : 0,
                        'position'         => isset($item['position']) ? $item['position'] : null,
                        'amount'           => isset($item['amount']) ? (float) $item['amount'] : 0,
                        'execution_status' => $execution_result,
                        'executed_at'      => $executed_at,
                        'execution_note'   => $execution_note,
                    ]
                );

                $executed_any = true;

            } catch (Throwable $e) {

                error_log('[RewardsPayoutExecution] Execution failed for player ' . ($item['player_id'] ?? 'unknown'));

                add_post_meta(
                    $tournament_id,
                    '_tb_rewards_payout_execution_record',
                    [
                        'tournament_id'    => $tournament_id,
                        'player_id'        => isset($item['player_id']) ? intval($item['player_id']) : 0,
                        'position'         => isset($item['position']) ? $item['position'] : null,
                        'amount'           => isset($item['amount']) ? (float) $item['amount'] : 0,
                        'execution_status' => 'failed',
                        'executed_at'      => time(),
                        'execution_note'   => 'Execution exception',
                    ]
                );

                continue;
            }
        }

        if ($executed_any) {
            add_post_meta($tournament_id, '_tb_rewards_payout_execution_timestamp', time());
        }

        add_post_meta($tournament_id, $lock_key, 1);

        return true;
    }
}
