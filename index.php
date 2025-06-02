<?php

/*
Plugin Name: Revive To Sky - Post old content to BlueSky
Plugin URI: https://dwinrhys.com/
Description: Post old blog posts to Bluesky on a regular basis.
Version: 1.0.2
Author: Dwi'n Rhys
Author URI: https://dwinrhys.com/
*/

define('DR_RTS_PATH', dirname(__FILE__));
define('DR_RTS_URL', plugins_url('', __FILE__));
define('DR_RTS_PLUGIN_VERSION', '1.0.2');
define( 'DR_BTS_MAX_IMAGE_SIZE', 1000000 );

add_action('admin_init', 'dr_rts_admin_init');

register_deactivation_hook(__FILE__, 'dr_rts_deactivate');
register_activation_hook(__FILE__, 'dr_rts_activate');

/**
 * Upon activation, create the cron job event.
 *
 * @return void
 */
function dr_rts_activate()
{
    if (! wp_next_scheduled('dr_rts_cron_hook')) {
        wp_schedule_event(time(), 'hourly', 'dr_rts_cron_hook');
    }
}

/**
 * Upon deactivation, delete the cron job event.
 *
 * @return void
 */
function dr_rts_deactivate()
{
    $timestamp = wp_next_scheduled('dr_rts_cron_hook');
    wp_unschedule_event($timestamp, 'dr_rts_cron_hook');
}

require_once DR_RTS_PATH . '/inc/core.php';
