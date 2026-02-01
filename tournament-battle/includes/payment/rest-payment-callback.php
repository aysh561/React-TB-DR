<?php

if (!defined('ABSPATH')) exit;

function tb_rest_payment_callback_route() {

    register_rest_route('tb/v1', '/payment/callback', [
        'methods'  => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function($req) {

            // shared-secret check (callback security)
            $received_secret = sanitize_text_field($req->get_param('secret'));
            if ($received_secret !== TB_CALLBACK_SECRET) {
                return ['success'=>false,'message'=>'Unauthorized callback'];
            }

            $params = $req->get_json_params();

            $ref_no = sanitize_text_field($params['ref_no'] ?? '');
            $amount = intval($params['amount'] ?? 0);
            $status = strtoupper(sanitize_text_field($params['status'] ?? ''));
            $raw    = wp_json_encode($params);

            if (!$ref_no || !$amount) {
                return ['success'=>false,'message'=>'Invalid callback'];
            }

            global $wpdb;
            $table = $wpdb->prefix.'tb_payments';

            // fetch DB row
            $row = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table WHERE ref_no=%s LIMIT 1", $ref_no),
                ARRAY_A
            );

            if (!$row) {
                return ['success'=>false,'message'=>'Record not found'];
            }

            $user_id       = intval($row['user_id']);
            $tournament_id = intval($row['tournament_id']);

            if (get_post_type($tournament_id)!=='tournament') {
                return ['success'=>false,'message'=>'Invalid tournament'];
            }

            // idempotency guard
            if ($row['status']==='approved') {
                return [
                    'success'=>true,
                    'message'=>'Already approved',
                    'status'=>'approved'
                ];
            }

            // amount match check
            $entry_fee = intval(get_post_meta($tournament_id, 'entry_fee', true));
            if ($entry_fee !== $amount) {

                tb_payment_set_state($tournament_id, $user_id, 'rejected');

                $wpdb->update(
                    $table,
                    [
                        'status'=>'rejected',
                        'api_raw_response'=>$raw,
                        'updated_at'=>current_time('mysql')
                    ],
                    ['id'=>$row['id']]
                );

                do_action('tb_payment_rejected',$tournament_id,$user_id,$ref_no);

                return ['success'=>false,'message'=>'Amount mismatch'];
            }

            // accepted success codes
            $success_codes = ['SUCCESS','PAID','0000','00'];

            if (in_array($status, $success_codes)) {

                $state = tb_payment_get_state($tournament_id, $user_id);

                // pending_api → approved
                if ($state === 'pending_api') {
                    tb_payment_set_state($tournament_id, $user_id, 'approved');
                }

                // pending_upload → under_review → approved
                if ($state === 'pending_upload') {
                    tb_payment_set_state($tournament_id, $user_id, 'under_review');
                    tb_payment_set_state($tournament_id, $user_id, 'approved');
                }

                $wpdb->update(
                    $table,
                    [
                        'status'=>'approved',
                        'api_raw_response'=>$raw,
                        'updated_at'=>current_time('mysql')
                    ],
                    ['id'=>$row['id']]
                );

                do_action('tb_payment_approved',$tournament_id,$user_id,$ref_no);

                return ['success'=>true,'message'=>'Auto-approved'];
            }

            // failed → reject
            tb_payment_set_state($tournament_id,$user_id,'rejected');

            $wpdb->update(
                $table,
                [
                    'status'=>'rejected',
                    'api_raw_response'=>$raw,
                    'updated_at'=>current_time('mysql')
                ],
                ['id'=>$row['id']]
            );

            do_action('tb_payment_rejected',$tournament_id,$user_id,$ref_no);

            return ['success'=>true,'message'=>'Auto-rejected'];
        }
    ]);
}

add_action('rest_api_init','tb_rest_payment_callback_route');
