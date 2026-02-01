<?php

if (!defined('ABSPATH')) exit;

function tb_validate_payment_amount($amount) {
    $a = intval($amount);
    return $a > 0 ? $a : false;
}

function tb_validate_payment_method($method) {
    $allowed = ['upload', 'api'];
    return in_array($method, $allowed) ? $method : false;
}

function tb_validate_payment_ref($ref) {
    $ref = sanitize_text_field($ref);
    return strlen($ref) > 0 ? $ref : false;
}

function tb_validate_payment_slip($file) {

    if (!isset($file) || empty($file['tmp_name'])) return false;

    $allowed_mime = ['image/jpeg','image/jpg','image/png'];
    $filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);

    if (!in_array($filetype['type'], $allowed_mime)) {
        return false;
    }

    $max_size = 3 * 1024 * 1024; // 3MB
    if ($file['size'] > $max_size) {
        return false;
    }

    $clean_name = sanitize_file_name($file['name']);
    if (!$clean_name || strlen($clean_name) < 3) {
        return false;
    }

    return true;
}
