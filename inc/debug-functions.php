<?php

/**
 * Simple debugging function so I can check if things are breaking
 *
 * @param  string  $message The message to log
 * @param  boolean $print_r Whether to print the message as a string or as a print_r
 * @return void
 */
function dr_rts_debug_log( $message ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        error_log( 'Revive to Sky: ' . $message );
    }
}