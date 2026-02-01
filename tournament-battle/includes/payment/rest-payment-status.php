<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_status_route() {

    register_rest_route('tb/v1', '/payment/status', [
        'methods'  => 'GET',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($req) {

            $tournament_id = intval($req->get_param('tournament_id'));

            if ($tournament_id <= 0) {
                return ['success' => false, 'message' => 'Invalid tournament'];
            }

            if (get_post_type($tournament_id) !== 'tournament') {
                return ['success' => false, 'message' => 'Invalid type'];
            }

            $user_id = get_current_user_id();
            $state   = tb_payment_get_state($tournament_id, $user_id);

            // FIX #3 — pass pending_api exactly as-is
            return [
                'success'       => true,
                'payment_state' => $state
            ];
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_payment_status_route');
