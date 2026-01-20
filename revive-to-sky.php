<?php

/*
Plugin Name: Revive To Sky - Post old content to Bluesky
Plugin URI: https://revivetosky.dwinrhys.com/
Description: Automatically post old blog posts to Bluesky, increasing traffic and engagement automatically.
Version: 1.1.1-RC1
Author: Dwi'n Rhys
Author URI: https://dwinrhys.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('REVIVETOSKY_PATH', dirname(__FILE__));
define('REVIVETOSKY_URL', plugins_url('', __FILE__));
define('REVIVETOSKY_PLUGIN_VERSION', '1.1.1-RC1');
define( 'REVIVETOSKY_MAX_IMAGE_SIZE', 1000000 );

register_deactivation_hook(__FILE__, 'revivetosky_deactivate');
register_activation_hook(__FILE__, 'revivetosky_activate');

/**
 * Upon activation, create the cron job event.
 *
 * @return void
 */
function revivetosky_activate()
{
    if (! wp_next_scheduled('revivetosky_cron_hook')) {
        wp_schedule_event(time(), 'hourly', 'revivetosky_cron_hook');
    }
}

/**
 * Upon deactivation, delete the cron job event.
 *
 * @return void
 */
function revivetosky_deactivate()
{
    $timestamp = wp_next_scheduled('revivetosky_cron_hook');
    wp_unschedule_event($timestamp, 'revivetosky_cron_hook');
}

require_once REVIVETOSKY_PATH . '/inc/core.php';
