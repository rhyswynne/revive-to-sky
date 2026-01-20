<?php

if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Cron handler to run every hour to check if we should run.
 * 
 * This checks if there is a transient present and runs it.
 * 
 * In this so it can be overwritten
 */
function revivetosky_cron_event()
{
    $last_posted = get_option('revivetosky_last_post_posted', 0);

    if (time() >= $last_posted) {
        revivetosky_debug_log('Running cron event');
        revivetosky_get_random_post_and_post_to_bluesky();
    } else {
        revivetosky_debug_log(time() . ' is before ' . $last_posted . ' do not run the cron event');
    }
}
add_action('revivetosky_cron_hook', 'revivetosky_cron_event');


/** 
 * Main function to post to Bluesky. 
 * This function:
 *
 * - Gets a random post
 * - Formats the Skeet
 * - POsts it to Bluesky
 * - Sets a transient to not post for X amount of time
 *
 * @return void
 */
function revivetosky_get_random_post_and_post_to_bluesky()
{
    // First Create the message
    $ptbs      = revivetosky_get_post_to_post_to_bluesky();
    $skeet     = revivetosky_form_skeet_to_post($ptbs);
    $skeeturls = revivetosky_get_urls($skeet);
    $skeethash = revivetosky_get_hashtags($skeet);
    $skeetment = revivetosky_get_mentions($skeet);

    revivetosky_debug_log('Post to bluesky with title ' . get_the_title($ptbs) . '.');
    revivetosky_debug_log('Skeet: ' . $skeet);
    revivetosky_debug_log('Hashtags: ' . print_r($skeethash, true));
    revivetosky_debug_log('Mentions: ' . print_r($skeetment, true));
    revivetosky_debug_log('Links: ' . print_r($skeeturls, true));

    $links    = revivetosky_create_link_card_array_from_url_array($skeeturls);
    $hashtags = revivetosky_create_hashtag_card_array_from_hashtag_array($skeethash);
    $mentions = revivetosky_create_mention_card_array_from_mention_array($skeetment);
    $facets = array_merge($hashtags, $links, $mentions);
    $facets = [
        'facets' =>
        $facets,
    ];

    // Then Post it to Bluesky
    // Get the Access Token and the DID
    $access_token = get_transient('revivetosky_access_token');
    $did = get_transient('revivetosky_did');

    if (!$access_token) {
        $refresh_token = get_transient('revivetosky_refresh_token');

        if ($refresh_token) {
            $response_body = revivetosky_get_refresh_token($refresh_token);

            if (array_key_exists('accessJwt', $response_body)) {
                $access_token = esc_attr($response_body['accessJwt']);
            } else {
                revivetosky_debug_log($auth_response->get_error_message());
            }
        } else {
            $auth_response = revivetosky_get_authorisation_token();

            if (array_key_exists('accessJwt', $auth_response)) {
                $access_token = esc_attr($auth_response['accessJwt']);
            } else {
                revivetosky_debug_log($auth_response->get_error_message());;
            }

            if (array_key_exists('did', $auth_response)) {
                $did = esc_attr($auth_response['did']);
            }
        }
    } else {
        revivetosky_debug_log('Using cached access token');
    }

    if ($access_token && $did) {

        $embed = revivetosky_create_link_card(get_the_id($ptbs), $access_token);

        $skeet_response = revivetosky_post_to_bluesky($skeet, $facets, $embed, $access_token, $did);

        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
        revivetosky_debug_log('Skeet Response: ' . print_r($skeet_response, true));

        //wp_die( print_r( $skeet_response ) );
        if (array_key_exists('uri', $skeet_response)) {
            if (array_key_exists('commit', $skeet_response)) {
                if (array_key_exists('rev', $skeet_response['commit'])) {
                    $handle = revivetosky_get_option('revivetosky_bluesky_handle');
                    $revivetosky_message_every_settings = revivetosky_get_option('revivetosky_message_every_settings');
                    $base_timescale = HOUR_IN_SECONDS;

                    if (array_key_exists('timeframe', $revivetosky_message_every_settings)) {
                        switch ($revivetosky_message_every_settings['timeframe']) {
                            case 'hours':
                                $base_timescale = HOUR_IN_SECONDS;
                                break;

                            case 'days':
                                $base_timescale = HOUR_IN_SECONDS * 24;
                                break;

                            case 'weeks':
                                $base_timescale = HOUR_IN_SECONDS * 24 * 7;
                                break;

                            default:
                                $base_timescale = HOUR_IN_SECONDS;
                                break;
                        }
                    }

                    if (array_key_exists('number', $revivetosky_message_every_settings)) {
                        $transient_time = $revivetosky_message_every_settings['number'] * $base_timescale;
                    } else {
                        $transient_time = HOUR_IN_SECONDS;
                    }

                    $url = $skeet_response['uri'];
                    $components = explode("/", $skeet_response['uri']);
                    $slug = end($components);

                    $repost_time = time() + $transient_time;

                    $message = "Post ID " . get_the_id($ptbs) . " (" . get_the_title($ptbs) . ") posted to bluesky at URL: https://bsky.app/profile/{$handle}/post/{$slug}/";
                    update_option('revivetosky_last_post_message', $message);
                    update_option('revivetosky_last_post_posted', $repost_time);
                }
            }
        }
    } else {
        revivetosky_debug_log("Missing access token or did");
    }
}
