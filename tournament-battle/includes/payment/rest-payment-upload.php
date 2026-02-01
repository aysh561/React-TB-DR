<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_upload_route() {

    register_rest_route('tb/v1', '/payment/upload', [
        'methods'  => 'POST',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($req) {

            $tournament_id = intval($req->get_param('tournament_id'));
            $file          = $_FILES['slip'] ?? null;

            if ($tournament_id <= 0) {
                return ['success' => false, 'message' => 'Invalid tournament'];
            }

            if (get_post_type($tournament_id) !== 'tournament') {
                return ['success' => false, 'message' => 'Invalid tournament type'];
            }

            if (!$file) {
                return ['success' => false, 'message' => 'Slip required'];
            }

            $user_id = get_current_user_id();

            $res = tb_payment_process_upload($tournament_id, $user_id, $file);

            return $res;
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_payment_upload_route');
