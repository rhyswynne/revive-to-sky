<?php

if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Wrapper function to get one option field
 *
 * @param  string $option_field  The option to return
 * @return mixed                 The option we've returned, or false
 */
function revivetosky_get_option($option_field)
{
    $all_options = get_option('revivetosky_settings');

    if (is_array($all_options)) {
        if (array_key_exists($option_field, $all_options)) {
            $option = $all_options[$option_field];
        } else {
            $option = false;
        }
    } else {
        $option = false;
    }

    return $option;
}


/**
 * Get all Categories in an array.
 * 
 * Wrapper function, returns false if empty
 *
 * @return mixed    array if we have 1 category, false if not.
 */
function revivetosky_get_categories()
{
    $categories = get_categories(array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    if (empty($categories)) {
        return false;
    }

    return $categories;
}

/**
 * Get all Tags in an array.
 * 
 * Wrapper function, returns false if empty
 *
 * @return mixed    array if we have 1 category, false if not.
 */
function revivetosky_get_tags()
{
    $categories = get_tags(array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    if (empty($categories)) {
        return false;
    }

    return $categories;
}

/**
 * Function to build the Newsletter box
 * 
 * Here so it isn't in the way. Also allows more than one of my plugins to be loaded on the same blog without any issues.
 */
if (!function_exists('revivetosky_print_newsletter_box')) {
    function revivetosky_print_newsletter_box()
    {
?>
        <div id="mlb2-21162946" class="ml-form-embedContainer ml-subscribe-form ml-subscribe-form-21162946">
            <div class="ml-form-align-center ">
                <div class="ml-form-embedWrapper embedForm">
                    <div class="ml-form-embedBody ml-form-embedBodyDefault row-form">

                        <div class="ml-form-embedContent" style=" ">

                            <h2><?php esc_html_e('Get Notified for Updates', 'revive-to-sky'); ?></h2>
                            <p><?php esc_html_e('Sign up below to receive updates to this plugin, as well as a monthly newsletter on SEO and WordPress news. Subscription is free and you can unsubscribe at any time.', 'revive-to-sky'); ?></p>

                        </div>

                        <form class="ml-block-form" action="https://assets.mailerlite.com/jsonp/609353/forms/142144868274144703/subscribe" data-code="" method="post" target="_blank">
                            <div class="ml-form-formContent">



                                <div class="ml-form-fieldRow ">
                                    <div class="ml-field-group ml-field-name">




                                        <!-- input -->
                                        <input aria-label="name" type="text" class="form-control" data-inputmask="" name="fields[name]" placeholder="Name" autocomplete="given-name">
                                        <!-- /input -->

                                        <!-- textarea -->

                                        <!-- /textarea -->

                                        <!-- select -->

                                        <!-- /select -->

                                        <!-- checkboxes -->

                                        <!-- /checkboxes -->

                                        <!-- radio -->

                                        <!-- /radio -->

                                        <!-- countries -->

                                        <!-- /countries -->





                                    </div>
                                </div>
                                <div class="ml-form-fieldRow ml-last-item">
                                    <div class="ml-field-group ml-field-email ml-validate-email ml-validate-required">




                                        <!-- input -->
                                        <input aria-label="email" aria-required="true" type="email" class="form-control" data-inputmask="" name="fields[email]" placeholder="Email" autocomplete="email">
                                        <!-- /input -->

                                        <!-- textarea -->

                                        <!-- /textarea -->

                                        <!-- select -->

                                        <!-- /select -->

                                        <!-- checkboxes -->

                                        <!-- /checkboxes -->

                                        <!-- radio -->

                                        <!-- /radio -->

                                        <!-- countries -->

                                        <!-- /countries -->





                                    </div>
                                </div>

                            </div>



                            <!-- Privacy policy -->

                            <!-- /Privacy policy -->

                            <input type="hidden" name="ml-submit" value="1">

                            <div class="ml-form-embedSubmit">

                                <button type="submit" class="primary dr_button dr_button_primary"><?php esc_html_e('Subscribe', 'revive-to-sky'); ?></button>

                                <button disabled="disabled" style="display: none;" type="button dr_button dr_button_disabled" class="loading">
                                    <div class="ml-form-embedSubmitLoad"></div>
                                    <span class="sr-only"><?php esc_html_e('Loading...', 'revive-to-sky'); ?></span>
                                </button>
                            </div>


                            <input type="hidden" name="anticsrf" value="true">
                        </form>
                    </div>

                    <div class="ml-form-successBody row-success" style="display: none">

                        <div class="ml-form-successContent">

                            <h2><?php esc_html_e('Thank you!', 'revive-to-sky'); ?></h2>

                            <p><?php esc_html_e('Thank you for your interest! To confirm, I have sent an email to you. Please click on that to subscribe!', 'revive-to-sky'); ?></p>


                        </div>

                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

/**
 * Get the post to post to Bluesky
 *
 * @param  $numberofposts  Optional - number of posts to return. I used the -1 for debugging.
 * @return mixed 
 */
function revivetosky_get_post_to_post_to_bluesky($numberofposts = 1)
{
    $included_categories = revivetosky_get_option('revivetosky_categories_to_include_settings');
    $excluded_categories = revivetosky_get_option('revivetosky_categories_to_exclude_settings');
    $included_tags       = revivetosky_get_option('revivetosky_tags_to_include_settings');
    $excluded_tags       = revivetosky_get_option('revivetosky_tags_to_exclude_settings');

    $post_args = array(
        'post_type'      => 'post',
        'orderby'        => 'rand',
        'posts_per_page' => $numberofposts,
        'ignore_sticky_posts' => 1
    );

    if ($included_categories) {
        $post_args['category__in'] = $included_categories;
    }

    if ($excluded_categories) {
        $post_args['category__not_in'] = $excluded_categories;
    }

    if ($included_tags) {
        $post_args['tag__in'] = $included_tags;
    }

    if ($excluded_tags) {
        $post_args['tag__not_in'] = $excluded_tags;
    }

    $post_to_skeet = new WP_Query($post_args);

    if ($post_to_skeet->have_posts()) {
        $post_to_skeet->the_post();
        return get_post();
    } else {
        return false;
    }
}


/**
 * Get the URLs in the post
 *
 * @param  string $text    The Skeet we are using
 * @return array  $urldata An array of all URLs
 */
function revivetosky_get_urls($text)
{

    $regex = '/(https?:\/\/[^\s]+)/';
    preg_match_all($regex, $text, $matches, PREG_OFFSET_CAPTURE);

    $urlData = array();

    foreach ($matches[0] as $match) {
        $url = $match[0];
        $start = $match[1];
        $end = $start + strlen($url);

        $urlData[] = array(
            'start' => $start,
            'end' => $end,
            'url' => $url,
        );
    }

    return $urlData;
}

/**
 * Get the Hashtags in the post
 *
 * @param  string $text    The Skeet we are using
 * @return array  $hashData An array of all hashtags
 */
function revivetosky_get_hashtags($text)
{

    $regex = '/#\w+/u';
    preg_match_all($regex, $text, $matches, PREG_OFFSET_CAPTURE);

    $hashData = array();

    foreach ($matches[0] as $match) {
        $tag = $match[0];
        $start = $match[1];
        $end = $start + strlen($tag);

        $hashData[] = array(
            'start' => $start,
            'end' => $end,
            'tag' => $tag,
        );
    }

    return $hashData;
}


/**
 * Get the Mentions in the post
 *
 * @param  string $text    The Skeet we are using
 * @return array  $mentions An array of all mentions
 */
function revivetosky_get_mentions($text)
{

    $regex = '/@[A-Za-z0-9._-]+/';
    preg_match_all($regex, $text, $matches, PREG_OFFSET_CAPTURE);

    //wp_die( "Text: " . $text . "Matches: " . print_r( $matches, true ) );

    $mentions = array();

    if (!empty($matches[0])) {

        $access_token = get_transient('revivetosky_access_token');

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
            }
        }

        if ($access_token) {
            foreach ($matches[0] as $match) {
                $did = revivetosky_get_did_from_handle(substr($match[0], 1), $access_token );
                
                if ( !is_wp_error( $did) ) {
                    $start = $match[1];
                    $end = $match[1] + strlen($match[0]);
    
                    $mentions[] = array(
                        'start' => $start,
                        'end' => $end,
                        'did' => $did,
                    );
                } else {
                    revivetosky_debug_log( $did->get_error_message() );
                }
            }
        }
    }

    return $mentions;
}


function revivetosky_create_link_card($post_id, $access_token)
{
    // The required fields for every embed card
    $card = [
        "uri" => get_the_permalink($post_id),
        "title" => get_the_title($post_id),
        "description" => get_the_excerpt($post_id),
    ];

    $embed = array();
    $image = false;

    if (has_post_thumbnail($post_id)) {
        $image_id = get_post_thumbnail_id($post_id);
        $image = revivetosky_upload_media_to_bluesky($image_id, $access_token);
    }

    if ($image) {
        $embed = [
            'embed' => [
                '$type' => 'app.bsky.embed.external',
                'external' => [
                    'uri' => $card['uri'],
                    'title' => $card['title'],
                    'description' => $card['description'],
                    'thumb' => $image,
                ],
            ],
        ];
    } else {
        $embed = [
            'embed' => [
                '$type' => 'app.bsky.embed.external',
                'external' => [
                    'uri' => $card['uri'],
                    'title' => $card['title'],
                    'description' => $card['description']
                ],
            ],
        ];
    }

    return $embed;
}


/**
 * Create a link card from the URL array
 *
 * @param  array $skeeturls The URL array
 * @return array $links     The link card
 */
function revivetosky_create_link_card_array_from_url_array($skeeturls)
{
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
    }

    return $links;
}


/**
 * Create a link card from the URL array
 *
 * @param  array $skeeturls The URL array
 * @return array $links     The link card
 */
function revivetosky_create_hashtag_card_array_from_hashtag_array($hashtags)
{
    $hashfacets = array();
    if (!empty($hashtags)) {
        foreach ($hashtags as $hashtag) {
            $a = [
                "index" => [
                    "byteStart" => $hashtag['start'],
                    "byteEnd" => $hashtag['end'],
                ],
                "features" => [
                    [
                        '$type' => "app.bsky.richtext.facet#tag",
                        'tag' => str_replace('#', '', $hashtag['tag']),
                    ],
                ],
            ];

            $hashfacets[] = $a;
        }
    }

    return $hashfacets;
}


/**
 * Create a Mentions card from the Mentions array
 *
 * @param  array $mentions          The Mentions array
 * @return array $mentionfacets     The mentions card
 */
function revivetosky_create_mention_card_array_from_mention_array($mentions)
{
    $mentionfacets = array();
    if (!empty($mentions)) {
        foreach ($mentions as $mention) {
            $a = [
                "index" => [
                    "byteStart" => $mention['start'],
                    "byteEnd" => $mention['end'],
                ],
                "features" => [
                    [
                        '$type' => "app.bsky.richtext.facet#mention",
                        'did' => $mention['did'],
                    ],
                ],
            ];

            $mentionfacets[] = $a;
        }
    }

    return $mentionfacets;
}
{
    $hashfacets = array();
    if (!empty($hashtags)) {
        foreach ($hashtags as $hashtag) {
            $a = [
                "index" => [
                    "byteStart" => $hashtag['start'],
                    "byteEnd" => $hashtag['end'],
                ],
                "features" => [
                    [
                        '$type' => "app.bsky.richtext.facet#tag",
                        'tag' => str_replace('#', '', $hashtag['tag']),
                    ],
                ],
            ];

            $hashfacets[] = $a;
        }
    }

    return $hashfacets;
}

/**
 * Get the Skeet to Post
 *
 * @param  object $ptbs  The post to post to Bluesky
 * @return string $skeet The Skeet to Post
 */
function revivetosky_form_skeet_to_post($ptbs)
{
    $skeet = revivetosky_get_option('revivetosky_message_to_send');

    // Allow for custom messages
    $skeet = apply_filters('revivetosky_message_to_send', $skeet, $ptbs);

    $skeet = str_replace('%%POSTTITLE%%', get_the_title($ptbs), $skeet);
    $skeet = str_replace('%%POSTURL%%', get_permalink($ptbs), $skeet);

    return $skeet;
}
