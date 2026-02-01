<?php
/**
 * Phase 13.1.4 — Wallet Ledger Credit
 * Sirf completed execution records ke liye ledger entries create karta hai.
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
            error_log('[RewardsWalletLedger] Invalid tournament ID');
            return false;
        }

        // Idempotency guard
        $lock_key = '_tb_rewards_wallet_ledger_locked';
        if (get_post_meta($tournament_id, $lock_key, true)) {
            return true;
        }

        // Dependency validation
        $execution_records = get_post_meta($tournament_id, '_tb_rewards_payout_execution_record', false);
        if (!is_array($execution_records) || empty($execution_records)) {
            error_log('[RewardsWalletLedger] Execution records missing');
            return false;
        }

        $credited_refs = get_post_meta($tournament_id, '_tb_rewards_wallet_ledger_refs', true);
        if (!is_array($credited_refs)) {
            $credited_refs = [];
        }

        $created_any = false;

        foreach ($execution_records as $record) {

            if (
                !is_array($record) ||
                empty($record['execution_status']) ||
                $record['execution_status'] !== 'completed'
            ) {
                continue;
            }

            // Unique reference hash for duplicate protection
            $ref_hash = md5(
                $tournament_id . '|' .
                ($record['player_id'] ?? '') . '|' .
                ($record['amount'] ?? '') . '|' .
                ($record['executed_at'] ?? '')
            );

            if (isset($credited_refs[$ref_hash])) {
                continue; // already credited
            }

            try {
                $ledger_entry = [
                    'tournament_id' => $tournament_id,
                    'player_id'     => intval($record['player_id']),
                    'amount'        => (float) $record['amount'],
                    'credit_type'   => 'tournament_reward',
                    'reference'     => $record,
                    'created_at'    => time(),
                ];

                add_post_meta($tournament_id, '_tb_rewards_wallet_ledger', $ledger_entry);

                // mark reference as credited (append-only index)
                $credited_refs[$ref_hash] = true;
                $created_any = true;

            } catch (Throwable $e) {
                error_log(
                    '[RewardsWalletLedger] Ledger credit failed for player ' .
                    ($record['player_id'] ?? 'unknown')
                );
                continue;
            }
        }

        if ($created_any) {
            update_post_meta($tournament_id, '_tb_rewards_wallet_ledger_refs', $credited_refs);
            add_post_meta($tournament_id, '_tb_rewards_wallet_ledger_created_at', time());
        }

        add_post_meta($tournament_id, $lock_key, 1);

        return true;
    }
}
