<?php

/**
 * Connect to Bluesky API and create a post
 *
 * @param string $message           The message to post to Bluesky
 * @param array $links              All links in the post
 * @param object $authorisation_obj The Authorisation object
 * @return array|WP_Error Response from the API or WP_Error on failure
 */
function dr_rts_post_to_bluesky($message, $links, $embed, $access_token, $did)
{

    // Build API endpoint URL
    $api_url = 'https://bsky.social/xrpc/com.atproto.repo.createRecord';

    // Build request body
    $request_body = array(
        'repo' => $did,
        'collection' => 'app.bsky.feed.post',
        'record' => array(
            'text' => $message,
            'createdAt' => gmdate('c'),
            '$type' => 'app.bsky.feed.post'
        )
    );

    if (!empty($links)) {
        $request_body['record'] = array_merge($request_body['record'], $links);
    }

    if (!empty($embed)) {
        $request_body['record'] = array_merge($request_body['record'], $embed);
    }

    // Build request args
    $args = array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => ' Bearer ' . $access_token
        ),
        'body' => wp_json_encode($request_body)
    );

    // Make API request
    $response = wp_remote_post($api_url, $args);

    // Check for errors
    if (is_wp_error($response)) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($response_code !== 200) {
        
        return new WP_Error(
            'api_error',
            sprintf(
                /* translators: 1: Error returned from Bluesky API. */
                __('Bluesky API error: %s', 'revive-to-sky'),
                isset($response_body['message']) ? $response_body['message'] : __('Unknown error', 'revive-to-sky')
            )
        );
    }

    return $response_body;
}


function dr_bts_upload_media_to_bluesky($image_id, $access_token)
{

    $mime = get_post_mime_type($image_id);
    $image_path = get_attached_file($image_id);
    $image_url = wp_get_attachment_image_url($image_id);
    $image_content = file_get_contents($image_path);

    if ( filesize( $image_content ) > DR_BTS_MAX_IMAGE_SIZE ) {
        return false;
    }

    // Build API endpoint URL
    $api_url = 'https://bsky.social/xrpc/com.atproto.repo.uploadBlob';

    // Build request args
    $args = array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => $mime,
            'Authorization' => ' Bearer ' . $access_token
        ),
        'body' => $image_content
    );

    $response = wp_remote_post($api_url, $args);

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    $image = $response_body['blob'];

    return $image;
}

/**
 * Get the Authorisation Token from Bluesky
 *
 * @return void
 */
function dr_rts_get_authorisation_token()
{
    // Get Bluesky credentials from options
    $handle       = dr_rts_get_option('dr_rts_bluesky_handle');
    $app_password = dr_rts_get_option('dr_rts_bluesky_app_password');

    // Validate credentials exist
    if (empty($handle) || empty($app_password)) {
        return new WP_Error('missing_credentials', __('Bluesky handle or app password not configured', 'revive-to-sky'));
    }

    // Build request body
    $request_body = array(
        'identifier' => $handle,
        'password' => $app_password
    );

    // Build request args
    $args = array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode($request_body),
        'timeout' => 30,
        'sslverify' => true
    );

    $auth_url = 'https://bsky.social/xrpc/com.atproto.server.createSession';

    // Make API request
    $response = wp_remote_post($auth_url, $args);

    // Check for errors
    if (is_wp_error($response)) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($response_code !== 200) {
        return new WP_Error(
            'api_error',
            sprintf(
                /* translators: 1: Error returned from Bluesky API. */
                __('Bluesky API error: %s', 'revive-to-sky'),
                isset($response_body['message']) ? $response_body['message'] : __('Unknown error', 'revive-to-sky')
            )
        );
    }

    return $response_body;
}



/**
 * Get the Authorisation Token from Bluesky
 *
 * @return void
 */
function dr_rts_get_refresh_token($refresh_token)
{
    // Get Bluesky credentials from options
    $handle       = dr_rts_get_option('dr_rts_bluesky_handle');
    $app_password = dr_rts_get_option('dr_rts_bluesky_app_password');

    // Validate credentials exist
    if (empty($handle) || empty($app_password)) {
        return new WP_Error('missing_credentials', __('Bluesky handle or app password not configured', 'revive-to-sky'));
    }

    // Build request args
    $args = array(
        'method' => 'POST',
        'headers' => array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $refresh_token
        ),
        'timeout' => 30,
        'sslverify' => true
    );

    $auth_url = 'https://bsky.social/xrpc/com.atproto.server.refreshSession';

    // Make API request
    $response = wp_remote_post($auth_url, $args);

    // Check for errors
    if (is_wp_error($response)) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($response_code !== 200) {
        return new WP_Error(
            'api_error',
            sprintf(
                /* translators: 1: Error returned from Bluesky API. */
                __('Bluesky API error: %s', 'revive-to-sky'),
                isset($response_body['message']) ? $response_body['message'] : __('Unknown error', 'revive-to-sky')
            )
        );
    }

    if (array_key_exists('accessJwt', $response_body)) {
        set_transient('rts_access_token', $response_body['accessJwt'], 600);
    }

    if (array_key_exists('refreshJwt', $response_body)) {
        set_transient('rts_refresh_token', $response_body['refreshJwt'], 2 * HOUR_IN_SECONDS);
    }

    if (array_key_exists('did', $response_body)) {
        set_transient('rts_did', $response_body, 2 * HOUR_IN_SECONDS);
    }

    if (!is_wp_error($response_body)) {
        set_transient('rts_obj', $response_body, 2 * HOUR_IN_SECONDS);
    }

    return $response_body;
}
