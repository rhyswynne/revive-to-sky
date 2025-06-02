<?php

/**
 * Add Action to the enqueue on the correct page
 *
 * @return void
 */
function dr_rts_enqueue_on_option_page() {
    add_action( 'admin_enqueue_scripts', 'dr_rts_enqueue_custom_admin_style' );
    wp_enqueue_script(
        'mailerlite-webforms',
        'https://groot.mailerlite.com/js/w/webforms.min.js?v176e10baa5e7ed80d35ae235be3d5024',
        array(),
        '1.0.0',
        true
    );
    wp_enqueue_script(
        'dr_rts_admin_js',
        DR_RTS_URL . '/inc/js/admin.js',
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
function dr_rts_enqueue_custom_admin_style() {
    wp_register_style( 'dr_rts_admin_css', DR_RTS_URL . '/inc/css/admin.css', false, '1.0.0' );
    wp_enqueue_style( 'dr_rts_admin_css' );
}