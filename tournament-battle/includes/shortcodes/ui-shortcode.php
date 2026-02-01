<?php

if (!defined('ABSPATH')) exit;

/**
 * Shortcode: [tb_tournament_ui]
 * ZERO DRIFT — sirf enqueue timing fix
 */
function tb_shortcode_tournament_ui($atts) {

    // ✅ ENQUEUE DIRECTLY (CRITICAL FIX)
    wp_enqueue_style(
        'tb-react-app',
        TB_CORE_URL . 'react-build/css/app.css',
        [],
        TB_CORE_VERSION
    );

    wp_enqueue_script(
        'tb-react-app',
        TB_CORE_URL . 'react-build/js/app.js',
        [],
        TB_CORE_VERSION,
        true
    );

    wp_localize_script('tb-react-app', 'TB_API', [
        'base' => site_url('/wp-json/tournament-battle/v1')
    ]);

    // --- EXISTING LOGIC (UNCHANGED) ---
    if (!isset($atts['id'])) {
        global $post;
        if (!$post) return '';
        $id = intval($post->ID);
    } else {
        $id = intval($atts['id']);
    }

    if ($id <= 0) return '';

    return '<div id="tb-app" data-tournament="' . esc_attr($id) . '"></div>';
}

add_shortcode('tb_tournament_ui', 'tb_shortcode_tournament_ui');
