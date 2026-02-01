<?php

if (!defined('ABSPATH')) exit;

function tb_register_tournament_meta_fields() {

    $fields = [

        // STRING
        'start_date_time'          => 'string',
        'tournament_status'        => 'string',
        'game_title'               => 'string',
        'tournament_short_desc'    => 'string',
        'tournament_host'          => 'string',
        'rules'                    => 'string',
        'bracket_type'             => 'string',
        'tournament_financial_note'=> 'string',

        // NUMBER
        'entry_fee'                => 'number',
        'total_rounds'             => 'number',
        'max_players'              => 'number',
        'joined_players'           => 'number',
        'winner_percent'           => 'number',
        'runnerup_percent'         => 'number',
        'site_deduction_percent'   => 'number',
        'refund_percent'           => 'number',
        'display_price'            => 'number',
        'tournament_banner'        => 'number',

        // BOOLEAN
        'final_payout_done'        => 'boolean',
        'enable_refund'            => 'boolean',

        // OBJECT
        'prize_distribution_json'  => 'object'
    ];

    foreach ($fields as $field => $type) {

        register_post_meta('tournament', $field, [
            'single'       => true,
            'show_in_rest' => true,
            'type'         => $type,
            'auth_callback'=> function() { return true; },
            'sanitize_callback' => function($value) use ($type) {

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
                        // HTML allowed for "rules"
                        $html_fields = ['rules'];
                        if (in_array($field, $html_fields)) {
                            return wp_kses_post($value);
                        }
                        return sanitize_text_field($value);
                }
            }
        ]);
    }
}

add_action('init', 'tb_register_tournament_meta_fields');
