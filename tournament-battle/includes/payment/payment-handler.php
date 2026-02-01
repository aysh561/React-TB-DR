<?php

if (!defined('ABSPATH')) exit;

/**
 * Write or update hybrid payment record
 */
function tb_payment_write_record($data) {
    global $wpdb;

    $table = $wpdb->prefix . 'tb_payments';

    $user_id        = intval($data['user_id']);
    $tournament_id  = intval($data['tournament_id']);
    $amount         = intval($data['amount']);
    $method         = sanitize_text_field($data['method']);
    $ref_no         = sanitize_text_field($data['ref_no']);
    $slip_url       = isset($data['slip_url']) ? esc_url_raw($data['slip_url']) : null;
    $status         = sanitize_text_field($data['status']);
    $attempt_no     = intval($data['attempt_no']);
    $dr_validation  = isset($data['dr_validation']) ? wp_json_encode($data['dr_validation']) : null;
    $api_raw        = isset($data['api_raw']) ? sanitize_textarea_field($data['api_raw']) : null;

    $now            = current_time('mysql');

    // Unique by ref_no
    $existing = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM $table WHERE ref_no = %s", $ref_no)
    );

    if ($existing) {

        $wpdb->update(
            $table,
            [
                'amount'          => $amount,
                'method'          => $method,
                'slip_url'        => $slip_url,
                'status'          => $status,
                'attempt_no'      => $attempt_no,
                'dr_validation'   => $dr_validation,
                'api_raw_response'=> $api_raw,
                'updated_at'      => $now
            ],
            ['id' => $existing]
        );

        return intval($existing);
    }

    // Fresh row = resets attempt window (FIX #3)
    $wpdb->insert(
        $table,
        [
            'user_id'          => $user_id,
            'tournament_id'    => $tournament_id,
            'amount'           => $amount,
            'method'           => $method,
            'slip_url'         => $slip_url,
            'ref_no'           => $ref_no,
            'status'           => $status,
            'attempt_no'       => $attempt_no,
            'dr_validation'    => $dr_validation,
            'api_raw_response' => $api_raw,
            'created_at'       => $now,
            'updated_at'       => $now
        ]
    );

    return $wpdb->insert_id;
}

/**
 * Attempt logic (2 attempts + 60-second window)
 */
function tb_payment_attempt_info($tournament_id, $user_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'tb_payments';

    $last = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT attempt_no, created_at FROM $table 
             WHERE user_id=%d AND tournament_id=%d 
             ORDER BY id DESC LIMIT 1",
            $user_id, $tournament_id
        ), ARRAY_A
    );

    if (!$last) {
        return [
            'attempt_no' => 1,
            'allowed'    => true,
            'reject'     => false
        ];
    }

    $attempt    = intval($last['attempt_no']);
    $created_ts = strtotime($last['created_at']);
    $now        = time();

    $expired = ($now - $created_ts) > 60;

    // If expired OR 2 attempts already used → reject
    if ($attempt >= 2 || $expired) {
        return [
            'attempt_no' => $attempt,
            'allowed'    => false,
            'reject'     => true
        ];
    }

    return [
        'attempt_no' => $attempt + 1,
        'allowed'    => true,
        'reject'     => false
    ];
}

/**
 * Handle Screenshot Upload channel
 */
function tb_payment_process_upload($tournament_id, $user_id, $file) {

    $amount = intval(get_post_meta($tournament_id, 'entry_fee', true));
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Invalid amount'];
    }

    // Attempt window logic
    $info = tb_payment_attempt_info($tournament_id, $user_id);

    if ($info['reject']) {
        tb_payment_set_state($tournament_id, $user_id, 'rejected');
        return ['success' => false, 'message' => 'Attempts exceeded'];
    }

    $attempt_no = $info['attempt_no'];

    // File validation
    if (!tb_validate_payment_slip($file)) {

        tb_payment_write_record([
            'user_id'       => $user_id,
            'tournament_id' => $tournament_id,
            'amount'        => $amount,
            'method'        => 'upload',
            'ref_no'        => 'upload_' . $user_id . '_' . time(),
            'status'        => 'pending_upload',
            'attempt_no'    => $attempt_no,
            'dr_validation' => ['error' => 'invalid_file']
        ]);

        if ($attempt_no >= 2) {
            tb_payment_set_state($tournament_id, $user_id, 'rejected');
        }

        return ['success' => false, 'message' => 'Invalid slip'];
    }

    // Upload image
    $u = wp_handle_upload($file, ['test_form' => false]);
    if (!isset($u['url'])) {
        return ['success' => false, 'message' => 'Upload failed'];
    }

    $slip_url = $u['url'];
    $ref_no   = 'upload_' . $user_id . '_' . time();

    // Write record
    tb_payment_write_record([
        'user_id'       => $user_id,
        'tournament_id' => $tournament_id,
        'amount'        => $amount,
        'method'        => 'upload',
        'slip_url'      => $slip_url,
        'ref_no'        => $ref_no,
        'status'        => 'pending_upload',
        'attempt_no'    => $attempt_no,
        'dr_validation' => ['file' => 'ok']
    ]);

    // Correct state (still pending_upload)
    tb_payment_set_state($tournament_id, $user_id, 'pending_upload');

    return [
        'success' => true,
        'message' => 'Slip uploaded',
        'slip_url'=> $slip_url,
        'ref_no'  => $ref_no
    ];
}

/**
 * API INIT FLOW — FIXED: STATE = pending_api
 */
function tb_payment_process_api_init($tournament_id, $user_id, $ref_no, $amount) {

    $amount = intval($amount);
    $ref_no = sanitize_text_field($ref_no);

    if (!$amount || !$ref_no) {
        return ['success' => false, 'message' => 'Invalid'];
    }

    $info = tb_payment_attempt_info($tournament_id, $user_id);

    if ($info['reject']) {
        tb_payment_set_state($tournament_id, $user_id, 'rejected');
        return ['success' => false, 'message' => 'Attempts exceeded'];
    }

    $attempt_no = $info['attempt_no'];

    tb_payment_write_record([
        'user_id'       => $user_id,
        'tournament_id' => $tournament_id,
        'amount'        => $amount,
        'method'        => 'api',
        'ref_no'        => $ref_no,
        'status'        => 'pending_api',
        'attempt_no'    => $attempt_no,
        'dr_validation' => ['api_init' => 'ok']
    ]);

    // FIX-1: CORRECT STATE
    tb_payment_set_state($tournament_id, $user_id, 'pending_api');

    return [
        'success' => true,
        'message' => 'API initiated',
        'ref_no'  => $ref_no
    ];
}
