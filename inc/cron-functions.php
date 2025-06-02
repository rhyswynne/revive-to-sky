<?php

/**
 * Cron handler to run every hour to check if we should run.
 * 
 * This checks if there is a transient present and runs it.
 * 
 * In this so it can be overwritten
 */
function dr_rts_cron_event()
{

    
    $last_posted = get_option('dr_rts_last_post_posted', 0);

    if ( time() >= $last_posted ) {
        dr_rts_debug_log( 'Running cron event' );
        dr_rts_get_random_post_and_post_to_bluesky();
    } else {
        dr_rts_debug_log( time() . ' is before ' . $last_posted . ' do not run the cron event' );
    }

}
add_action('dr_rts_cron_hook', 'dr_rts_cron_event');


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
function dr_rts_get_random_post_and_post_to_bluesky()
{
    // First Create the message
    $ptbs  = dr_rts_get_post_to_post_to_bluesky();
    $skeet = dr_rts_get_option('dr_rts_message_to_send');

    $skeet = str_replace('%%POSTTITLE%%', get_the_title($ptbs), $skeet);
    $skeet = str_replace('%%POSTURL%%', get_permalink($ptbs), $skeet);
    $skeeturls = dt_rts_get_urls($skeet);

    dr_rts_debug_log( 'Post to bluesky with title ' . get_the_title($ptbs) . '.' );
    dr_rts_debug_log( 'Skeet: ' . $skeet );

    $links = array();
    if (!empty($skeeturls)) {
        foreach ($skeeturls as $url) {
            $a = [
                "index" => [
                    "byteStart" => $url['start'],
                    "byteEnd" => $url['end'],
                ],
                "features" => [
                    [
                        '$type' => "app.bsky.richtext.facet#link",
                        'uri' => $url['url'],
                    ],
                ],
            ];

            $links[] = $a;
        }
        $links = [
            'facets' =>
            $links,
        ];
    }

    // Then Post it to Bluesky
    // Get the Access Token and the DID
    $access_token = get_transient('rts_access_token');
    $did = get_transient('rts_did');

    if (!$access_token) {
        $refresh_token = get_transient('rts_refresh_token');

        if ($refresh_token) {
            $response_body = dr_rts_get_refresh_token($refresh_token);

            if (array_key_exists('accessJwt', $response_body)) {
                $access_token = esc_attr( $response_body['accessJwt'] );
            } else {
                wp_die( esc_attr( $auth_response->get_error_message() ) );
            }
        } else {
            $auth_response = dr_rts_get_authorisation_token();

            if (array_key_exists('accessJwt', $auth_response)) {
                $access_token = esc_attr($auth_response['accessJwt']);
            } else {
                wp_die( esc_attr( $auth_response->get_error_message() ) );
            }

            if (array_key_exists('did', $auth_response)) {
                $did = esc_attr($auth_response['did']);
            }
        }
    }

    if ($access_token && $did) {

        $embed = dt_rts_create_link_card(get_the_id($ptbs), $access_token);

        $skeet_response = dr_rts_post_to_bluesky($skeet, $links, $embed, $access_token, $did);

        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
        dr_rts_debug_log( 'Skeet Response: ' . print_r( $skeet_response, true ) );

        //wp_die( print_r( $skeet_response ) );
        if (array_key_exists('uri', $skeet_response)) {
            if (array_key_exists('commit', $skeet_response)) {
                if (array_key_exists('rev', $skeet_response['commit'])) {
                    $handle = dr_rts_get_option('dr_rts_bluesky_handle');
                    $dr_rts_message_every_settings = dr_rts_get_option('dr_rts_message_every_settings');
                    $base_timescale = HOUR_IN_SECONDS;

                    if (array_key_exists('timeframe', $dr_rts_message_every_settings)) {
                        switch ($dr_rts_message_every_settings['timeframe']) {
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

                    if (array_key_exists('number', $dr_rts_message_every_settings)) {
                        $transient_time = $dr_rts_message_every_settings['number'] * $base_timescale;
                    } else {
                        $transient_time = HOUR_IN_SECONDS;
                    }

                    $url = $skeet_response['uri'];
                    $components = explode("/", $skeet_response['uri']);
                    $slug = end($components);

                    $repost_time = time() + $transient_time;

                    $message = "Post ID " . get_the_id($ptbs) . " (" . get_the_title($ptbs) . ") posted to bluesky at URL: https://bsky.app/profile/{$handle}/post/{$slug}/";
                    update_option('dr_rts_last_post_message', $message);
                    update_option('dr_rts_last_post_posted', $repost_time);
                }
            }
        }
    } else {
        dr_rts_debug_log( "Missing access token or did" );
    }
}
