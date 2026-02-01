<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_logs_route() {

    register_rest_route('tb/v1', '/payment/logs', [
        'methods'  => 'GET',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($req) {

            $tournament_id = intval($req->get_param('tournament_id'));

            if ($tournament_id <= 0) {
                return ['success' => false, 'message' => 'Invalid ID'];
            }

            if (get_post_type($tournament_id) !== 'tournament') {
                return ['success' => false, 'message' => 'Invalid type'];
            }

            $user_id = get_current_user_id();

            global $wpdb;
            $table = $wpdb->prefix . 'tb_payments';

            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT amount, method, slip_url, ref_no, status, attempt_no, created_at, updated_at 
                     FROM $table 
                     WHERE user_id=%d AND tournament_id=%d
                     ORDER BY id DESC",
                    $user_id, $tournament_id
                ), ARRAY_A
            );

            // FIX #4 — ensure states are ORIGINAL as stored (pending_api / pending_upload not mixed)
            foreach ($rows as &$r) {
                $r['status'] = sanitize_text_field($r['status']);
            }

            return [
                'success' => true,
                'logs'    => $rows
            ];
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_payment_logs_route');
