<?php

if (!defined('ABSPATH')) exit;

function tb_register_tournament_cpt() {

    $labels = [
        'name'               => 'Tournaments',
        'singular_name'      => 'Tournament',
        'add_new'            => 'Add Tournament',
        'add_new_item'       => 'Add New Tournament',
        'edit_item'          => 'Edit Tournament',
        'new_item'           => 'New Tournament',
        'view_item'          => 'View Tournament',
        'search_items'       => 'Search Tournaments',
        'not_found'          => 'No tournaments found',
        'not_found_in_trash' => 'No tournaments found in trash',
    ];

    $args = [
        'label'               => 'Tournaments',
        'labels'              => $labels,
        'public'              => true,
        'show_in_rest'        => true,
        'supports'            => ['title', 'thumbnail', 'custom-fields'],
        'has_archive'         => false,
        'menu_icon'           => 'dashicons-tickets-alt',
        'rewrite'             => [
            'slug'       => 'tournament',
            'with_front' => false
        ],
        'capability_type'     => 'post'
    ];

    register_post_type('tournament', $args);
}

add_action('init', 'tb_register_tournament_cpt');
