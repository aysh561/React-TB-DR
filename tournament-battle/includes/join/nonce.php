<?php

if (!defined('ABSPATH')) exit;

function tb_get_join_nonce($tournament_id) {
    return wp_create_nonce('tb_join_nonce_' . intval($tournament_id));
}
