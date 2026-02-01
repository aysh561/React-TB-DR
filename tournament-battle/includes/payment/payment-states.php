<?php

if (!defined('ABSPATH')) exit;

function tb_payment_get_state($tournament_id, $user_id) {
    $key = 'tb_payment_status_' . intval($tournament_id);
    $val = get_user_meta($user_id, $key, true);
    return $val ? sanitize_text_field($val) : 'none';
}

function tb_payment_set_state($tournament_id, $user_id, $new_state) {

    $allowed_states = [
        'none',
        'pending_upload',
        'pending_api',
        'under_review',
        'approved',
        'rejected'
    ];

    if (!in_array($new_state, $allowed_states)) {
        return false;
    }

    $old = tb_payment_get_state($tournament_id, $user_id);

    // STRICT TRANSITIONS (UPDATED)
    $valid_map = [
        'none' => ['pending_upload', 'pending_api'],

        'pending_upload' => ['under_review'],
        'pending_api'    => ['under_review'],

        'under_review' => ['approved', 'rejected'],

        'approved' => [],
        'rejected' => []
    ];

    if (!in_array($new_state, $valid_map[$old])) {
        return false;
    }

    $key = 'tb_payment_status_' . intval($tournament_id);
    update_user_meta($user_id, $key, $new_state);

    return true;
}
