<?php
/**
 * Phase 13.1.5 — Admin Audit Log
 * Final immutable audit trail for rewards execution.
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
            error_log('[RewardsAdminAudit] Invalid tournament ID');
            return false;
        }

        // Idempotency guard
        $lock_key = '_tb_rewards_admin_audit_locked';
        if (get_post_meta($tournament_id, $lock_key, true)) {
            return true;
        }

        // Dependency validation
        $eligible_players   = get_post_meta($tournament_id, '_tb_rewards_eligible_players', true);
        $prize_snapshot     = get_post_meta($tournament_id, '_tb_rewards_prize_snapshot', true);
        $payout_queue       = get_post_meta($tournament_id, '_tb_rewards_payout_queue', false);
        $execution_records  = get_post_meta($tournament_id, '_tb_rewards_payout_execution_record', false);
        $wallet_ledger      = get_post_meta($tournament_id, '_tb_rewards_wallet_ledger', false);

        if (
            !is_array($eligible_players) || empty($eligible_players) ||
            !is_array($prize_snapshot)   || empty($prize_snapshot)   ||
            !is_array($payout_queue)     || empty($payout_queue)     ||
            !is_array($execution_records)|| empty($execution_records)||
            !is_array($wallet_ledger)    || empty($wallet_ledger)
        ) {
            error_log('[RewardsAdminAudit] Required reward data missing');
            return false;
        }

        // Prepare consolidated audit summary (no full duplication)
        $audit_entry = [
            'tournament_id'               => $tournament_id,
            'eligible_players_count'      => count($eligible_players),
            'prize_snapshot'              => $prize_snapshot,
            'payout_queue_count'          => count($payout_queue),
            'execution_records_count'     => count($execution_records),
            'wallet_ledger_entries_count' => count($wallet_ledger),
            'audit_generated_at'          => time(),
        ];

        try {
            // Append-only audit log
            add_post_meta($tournament_id, '_tb_rewards_admin_audit_log', $audit_entry);
            add_post_meta($tournament_id, $lock_key, 1);
        } catch (Throwable $e) {
            error_log('[RewardsAdminAudit] Audit log write failed');
            return false;
        }

        return true;
    }
}
