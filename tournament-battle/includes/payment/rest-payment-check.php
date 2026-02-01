<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_check_route() {

    register_rest_route('tb/v1', '/payment/check', [
        'methods'  => 'GET',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($req) {

            $tournament_id = intval($req->get_param('tournament_id'));
            $ref_no        = sanitize_text_field($req->get_param('ref_no'));

            if ($tournament_id <= 0 || !$ref_no) {
                return ['success' => false, 'message' => 'Invalid'];
            }

            if (get_post_type($tournament_id) !== 'tournament') {
                return ['success' => false, 'message' => 'Invalid tournament'];
            }

            global $wpdb;
            $table = $wpdb->prefix . 'tb_payments';

            $row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT status, attempt_no FROM $table 
                     WHERE ref_no=%s AND tournament_id=%d 
                     ORDER BY id DESC LIMIT 1",
                    $ref_no, $tournament_id
                ), ARRAY_A
            );

            $user_id = get_current_user_id();

            return [
                'success'      => true,
                'status'       => tb_payment_get_state($tournament_id, $user_id), // FIX #2
                'db_status'    => $row['status'] ?? 'none',                       // still available
                'attempt_no'   => intval($row['attempt_no'] ?? 0)
            ];
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_payment_check_route');
