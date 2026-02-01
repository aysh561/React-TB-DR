<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_reject_route() {

    register_rest_route('tb/v1', '/payment/reject', [
        'methods'  => 'POST',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'callback' => function($req) {

            // nonce validation
            $nonce = sanitize_text_field($req->get_param('_wpnonce'));
            if (!wp_verify_nonce($nonce, 'tb_admin_payment_action')) {
                return ['success'=>false,'message'=>'Invalid nonce'];
            }

            $params        = $req->get_json_params();
            $tournament_id = intval($params['tournament_id'] ?? 0);
            $user_id       = intval($params['user_id'] ?? 0);
            $ref_no        = sanitize_text_field($params['ref_no'] ?? '');

            if ($tournament_id<=0 || $user_id<=0 || !$ref_no) {
                return ['success'=>false,'message'=>'Invalid input'];
            }

            if (get_post_type($tournament_id)!=='tournament') {
                return ['success'=>false,'message'=>'Invalid tournament'];
            }

            global $wpdb;
            $table = $wpdb->prefix.'tb_payments';

            // fetch payment row
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $table WHERE ref_no=%s AND tournament_id=%d LIMIT 1",
                    $ref_no, $tournament_id
                ),
                ARRAY_A
            );

            if (!$row) {
                return ['success'=>false,'message'=>'Payment record not found'];
            }

            // ownership check
            if (intval($row['user_id']) !== $user_id) {
                return ['success'=>false,'message'=>'Cross-approval blocked'];
            }

            // enforce under_review state
            $state = tb_payment_get_state($tournament_id, $user_id);
            if ($state !== 'under_review') {
                return ['success'=>false,'message'=>'State must be under_review'];
            }

            // update DB
            $wpdb->update(
                $table,
                [
                    'status'=>'rejected',
                    'dr_validation'=>wp_json_encode(['admin'=>'rejected']),
                    'updated_at'=>current_time('mysql')
                ],
                ['id'=>$row['id']]
            );

            // update meta
            tb_payment_set_state($tournament_id, $user_id, 'rejected');

            // hook
            do_action('tb_payment_rejected', $tournament_id, $user_id, $ref_no);

            return ['success'=>true,'message'=>'Payment rejected'];
        }
    ]);
}

add_action('rest_api_init','tb_rest_payment_reject_route');
