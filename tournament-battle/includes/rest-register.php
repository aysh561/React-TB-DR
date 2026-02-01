<?php

if (!defined('ABSPATH')) exit;

function tb_register_tournament_rest_fields() {

    $fields = [

        // STRING
        'start_date_time'           => 'string',
        'tournament_status'         => 'string',
        'game_title'                => 'string',
        'tournament_short_desc'     => 'string',
        'tournament_host'           => 'string',
        'rules'                     => 'string',
        'bracket_type'              => 'string',
        'tournament_financial_note' => 'string',

        // NUMBER
        'entry_fee'                 => 'number',
        'total_rounds'              => 'number',
        'max_players'               => 'number',
        'joined_players'            => 'number',
        'winner_percent'            => 'number',
        'runnerup_percent'          => 'number',
        'site_deduction_percent'    => 'number',
        'refund_percent'            => 'number',
        'display_price'             => 'number',
        'tournament_banner'         => 'number',

        // BOOLEAN
        'final_payout_done'         => 'boolean',
        'enable_refund'             => 'boolean',

        // OBJECT
        'prize_distribution_json'   => 'object'
    ];

    foreach ($fields as $field => $type) {

        register_rest_field('tournament', $field, [

            'get_callback' => function($obj) use ($field, $type) {

                $value = get_post_meta($obj['id'], $field, true);

                switch ($type) {

                    case 'number':
                        return intval($value);

                    case 'boolean':
                        return boolval($value);

                    case 'object':
                        if (is_array($value)) {
                            return $value;
                        }
                        $decoded = json_decode($value, true);
                        return is_array($decoded) ? $decoded : [];

                    case 'string':
                    default:
                        return $value;
                }
            },

            'update_callback' => function($value, $post_obj) use ($field, $type) {

                switch ($type) {

                    case 'number':
                        return update_post_meta($post_obj->ID, $field, intval($value));

                    case 'boolean':
                        return update_post_meta($post_obj->ID, $field, boolval($value));

                    case 'object':
                        if (is_array($value)) {
                            return update_post_meta($post_obj->ID, $field, $value);
                        }
                        $decoded = json_decode($value, true);
                        return update_post_meta($post_obj->ID, $field, is_array($decoded) ? $decoded : []);

                    case 'string':
                    default:
                        $html_allowed = ['rules'];
                        if (in_array($field, $html_allowed)) {
                            $clean = wp_kses_post($value);
                        } else {
                            $clean = sanitize_text_field($value);
                        }
                        return update_post_meta($post_obj->ID, $field, $clean);
                }
            },

            'schema' => [
                'type' => $type
            ]
        ]);

    }
}

add_action('rest_api_init', 'tb_register_tournament_rest_fields');
add_action('rest_api_init', function () {

    register_rest_route('tournament-battle/v1', '/tournaments', [
        'methods'  => 'GET',
        'callback' => 'tb_rest_get_tournaments',
        'permission_callback' => '__return_true',
    ]);

});

function tb_rest_get_tournaments() {

    $args = [
        'post_type'      => 'tournament',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
    ];

    $query = new WP_Query($args);
    $tournaments = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $tournaments[] = [
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'link'  => get_permalink(),
            ];
        }
        wp_reset_postdata();
    }

    return [
        'status' => 'ok',
        'data'   => $tournaments,
    ];
}