<?php

if (!defined('ABSPATH')) exit;

/**
 * Check if user can join a tournament
 */
function tb_user_can_join($tournament_id, $user_id) {

    if (!$user_id || $user_id <= 0) return false;

    // Type validation
    if (get_post_type($tournament_id) !== 'tournament') {
        return false;
    }

    if (tb_is_tournament_full($tournament_id)) {
        return false;
    }

    if (tb_is_join_expired($tournament_id)) {
        return false;
    }

    $status = tb_get_join_status($tournament_id, $user_id);
    if ($status !== 'none') {
        return false;
    }

    return true;
}

/**
 * Tournament full?
 */
function tb_is_tournament_full($tournament_id) {
    $max = intval(get_post_meta($tournament_id, 'max_players', true));
    $joined = intval(get_post_meta($tournament_id, 'joined_players', true));
    return ($max > 0 && $joined >= $max);
}

/**
 * Join expired?
 */
function tb_is_join_expired($tournament_id) {
    $start = get_post_meta($tournament_id, 'start_date_time', true);
    if (!$start) return false;
    return (time() >= strtotime($start));
}

/**
 * Get join status (none | joined_unpaid)
 */
function tb_get_join_status($tournament_id, $user_id) {
    $key = 'tb_join_status_' . intval($tournament_id);
    $val = get_user_meta($user_id, $key, true);
    return $val ? sanitize_text_field($val) : 'none';
}

/**
 * Maintain user’s tournament history
 */
function tb_update_user_tournament_history($user_id, $tournament_id) {

    $key = 'tb_user_tournament_history';
    $history = get_user_meta($user_id, $key, true);

    if (!is_array($history)) $history = [];

    $clean = [];
    foreach ($history as $h) {
        $id = intval($h);
        if ($id > 0 && !in_array($id, $clean)) {
            $clean[] = $id;
        }
    }

    if (!in_array($tournament_id, $clean)) {
        $clean[] = intval($tournament_id);
    }

    update_user_meta($user_id, $key, $clean);
}

/**
 * JOIN ACTION (MANDATED FINAL VERSION)
 */
function tb_join_tournament($tournament_id, $user_id, $nonce) {

    // Nonce verify
    if (!wp_verify_nonce($nonce, 'tb_join_nonce_' . $tournament_id)) {
        return [
            'success' => false,
            'message' => 'Invalid request.'
        ];
    }

    // Type validation
    if (get_post_type($tournament_id) !== 'tournament') {
        return [
            'success' => false,
            'message' => 'Invalid tournament.'
        ];
    }

    // Permission check
    if (!tb_user_can_join($tournament_id, $user_id)) {
        return [
            'success' => false,
            'message' => 'Join not allowed.'
        ];
    }

    // Save state (MANDATED → joined_unpaid)
    $key = 'tb_join_status_' . intval($tournament_id);
    update_user_meta($user_id, $key, 'joined_unpaid');

    // ATOMIC INCREMENT (MANDATORY DIRECTIVE)
    global $wpdb;
    $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->postmeta}
             SET meta_value = meta_value + 1
             WHERE post_id = %d AND meta_key = 'joined_players'",
            $tournament_id
        )
    );

    // Update user history
    tb_update_user_tournament_history($user_id, $tournament_id);

    // Required hook
    do_action('tb_user_joined', $tournament_id, $user_id);

    return [
        'success' => true,
        'message' => 'Join successful.'
    ];
}
