<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_init_route() {

    register_rest_route('tb/v1', '/payment/init', [
        'methods'  => 'POST',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function($req) {

            $params = $req->get_json_params();

            $tournament_id = intval($params['tournament_id'] ?? 0);
            $amount        = intval($params['amount'] ?? 0);
            $ref_no        = sanitize_text_field($params['ref_no'] ?? '');
            $method        = sanitize_text_field($params['method'] ?? '');

            if ($tournament_id <= 0) {
                return ['success' => false, 'message' => 'Invalid tournament'];
            }

            if (get_post_type($tournament_id) !== 'tournament') {
                return ['success' => false, 'message' => 'Invalid tournament type'];
            }

            if (!tb_validate_payment_method($method)) {
                return ['success' => false, 'message' => 'Invalid method'];
            }

            $user_id = get_current_user_id();

            // FIX #1 — upload init must set pending_upload + generate dummy ref_no
            if ($method === 'upload') {

                $dummy_ref = 'upload_' . $user_id . '_' . time();

                tb_payment_write_record([
                    'user_id'       => $user_id,
                    'tournament_id' => $tournament_id,
                    'amount'        => $amount,
                    'method'        => 'upload',
                    'ref_no'        => $dummy_ref,
                    'status'        => 'pending_upload',
                    'attempt_no'    => 1,
                    'dr_validation' => ['init' => 'upload_init']
                ]);

                tb_payment_set_state($tournament_id, $user_id, 'pending_upload');

                return [
                    'success' => true,
                    'message' => 'Upload payment initialized',
                    'ref_no'  => $dummy_ref,
                    'next'    => 'upload'
                ];
            }

            // API INIT
            $res = tb_payment_process_api_init($tournament_id, $user_id, $ref_no, $amount);
            return $res;
        }
    ]);
}

add_action('rest_api_init', 'tb_rest_payment_init_route');
