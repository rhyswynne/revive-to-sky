<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Add Action to the enqueue on the correct page
 *
 * @return void
 */
function revivetosky_enqueue_on_option_page() {
    add_action( 'admin_enqueue_scripts', 'revivetosky_enqueue_custom_admin_style' );
    wp_enqueue_script(
        'mailerlite-webforms',
        'https://groot.mailerlite.com/js/w/webforms.min.js?v176e10baa5e7ed80d35ae235be3d5024',
        array(),
        '1.0.0',
        true
    );
    wp_enqueue_script(
        'revivetosky_admin_js',
        REVIVETOSKY_URL . '/inc/js/admin.js',
        array( 'mailerlite-webforms' ),
        '1.0.0',
        true
    );
}


/**
 * Register and enqueue a the Custom RTS Stylesheet in the WordPress admin.
 *
 * @return void
 */
function revivetosky_enqueue_custom_admin_style() {
    wp_register_style( 'revivetosky_admin_css', REVIVETOSKY_URL . '/inc/css/admin.css', false, '1.0.0' );
    wp_enqueue_style( 'revivetosky_admin_css' );
}