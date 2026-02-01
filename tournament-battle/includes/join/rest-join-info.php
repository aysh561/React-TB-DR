<?php

if (!defined('ABSPATH')) exit;

function tb_rest_join_info_route() {

    register_rest_route('tb/v1', '/tournament/(?P<id>\d+)/join-info', [
        'methods'  => 'GET',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($request) {

            $tournament_id = intval($request['id']);

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
                    'message' => 'Invalid tournament.'
                ];
            }

            $user_id = get_current_user_id();

            return [
                'nonce'          => tb_get_join_nonce($tournament_id),
                'join_status'    => tb_get_join_status($tournament_id, $user_id),
                'is_full'        => tb_is_tournament_full($tournament_id),
                'is_expired'     => tb_is_join_expired($tournament_id),
                'max_players'    => intval(get_post_meta($tournament_id, 'max_players', true)),
                'joined_players' => intval(get_post_meta($tournament_id, 'joined_players', true))
            ];
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_join_info_route');
