<?php

if (!defined('ABSPATH')) exit;

function tb_payment_create_table() {
    global $wpdb;

    $table = $wpdb->prefix . 'tb_payments';
    $charset = $wpdb->get_charset_collate();

    $sql = "
        CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            tournament_id BIGINT UNSIGNED NOT NULL,
            amount INT UNSIGNED NOT NULL,
            method VARCHAR(20) NOT NULL,
            slip_url TEXT NULL,
            ref_no VARCHAR(100) NULL,
            status VARCHAR(50) NOT NULL,
            attempt_no INT UNSIGNED NOT NULL DEFAULT 1,
            dr_validation JSON NULL,
            api_raw_response TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY ref_no_unique (ref_no),
            INDEX user_idx (user_id),
            INDEX tour_idx (tournament_id),
            INDEX status_idx (status)
        ) $charset;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
