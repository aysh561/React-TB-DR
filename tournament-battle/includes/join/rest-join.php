<?php

if (!defined('ABSPATH')) exit;

function tb_rest_join_route() {

    register_rest_route('tb/v1', '/join', [
        'methods'  => 'POST',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($request) {

            $params = $request->get_json_params();

            $tournament_id = intval($params['tournament_id'] ?? 0);
            $nonce         = sanitize_text_field($params['nonce'] ?? '');

            if ($tournament_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid tournament ID.'
                ];
            }

            // Type validation
            if (get_post_type($tournament_id) !== 'tournament') {
                return [
                    'success' => false,
                    'message' => 'Invalid tournament type.'
                ];
            }

            $user_id = get_current_user_id();

            $result = tb_join_tournament($tournament_id, $user_id, $nonce);

            return $result;
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_join_route');
