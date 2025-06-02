<?php

/**
 * Add Action to the enqueue on the correct page
 *
 * @return void
 */
function dr_rts_enqueue_on_option_page() {
    add_action( 'admin_enqueue_scripts', 'dr_rts_enqueue_custom_admin_style' );
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
