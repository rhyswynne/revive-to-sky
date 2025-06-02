<?php

function dr_rts_admin_init()
{

    if (array_key_exists('skeet', $_GET)) {
        dr_rts_get_random_post_and_post_to_bluesky();    
    }

    if ( array_key_exists( 'get_random_post', $_GET ) ) {
        $random_post = dr_rts_get_post_to_post_to_bluesky( -1 );
        //wp_die( print_r( $random_post ) );
    }
} add_action( 'admin_init', 'dr_rts_admin_init' );